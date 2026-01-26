<?php

namespace App\Http\Controllers;

use App\Models\LmsItem;
use App\Models\LmsAccessLog; // Penting untuk mencatat log
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // <--- PENTING BUAT GROUP BY



class LmsController extends Controller
{
    // =========================================================================
    // BAGIAN 1: USER / SISWA SIDE (Menampilkan Materi)
    // =========================================================================

public function show($slug)
    {
        // 1. Cari item saat ini
        $item = LmsItem::where('slug', $slug)->firstOrFail();

        // 2. Analytics (Tetap sama)
        if (Auth::check() && $item->type !== 'folder') {
            LmsAccessLog::firstOrCreate([
                'user_id'     => Auth::id(),
                'lms_item_id' => $item->id,
                'action_type' => 'view',
            ], [
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        }

        // --- LOGIKA BARU: CARI PREVIOUS & NEXT ---
        $prevItem = null;
        $nextItem = null;

        // Kita hanya butuh navigasi jika item ini punya orang tua (berada dalam folder)
        if ($item->parent_id) {
            
            // --- BAGIAN INI YANG DIUBAH ---
            $siblings = LmsItem::where('parent_id', $item->parent_id)
                                ->where('is_active', true)
                                // HAPUS baris ->orderBy('type', 'asc') 
                                
                                // GANTI JADI SEPERTI INI:
                                ->orderBy('order', 'asc') // 1. Prioritas utama: Kolom urutan manual
                                ->orderBy('id', 'asc')    // 2. Prioritas kedua: Siapa yang dibuat duluan (ID kecil di awal)
                                ->get();
            // -----------------------------

            $currentIndex = $siblings->search(function($sibling) use ($item) {
                return $sibling->id === $item->id;
            });

            $prevItem = $siblings->get($currentIndex - 1);
            $nextItem = $siblings->get($currentIndex + 1);
        }
        // 3. TENTUKAN VIEW
        if ($item->type === 'folder') {
            $children = $item->children()->where('is_active', true)->get();
            return view('lms.folder', compact('item', 'children')); // Folder gak butuh navigasi next/prev biasanya
        } 
        
        // Kirim $prevItem dan $nextItem ke view content
        return view('lms.content', compact('item', 'prevItem', 'nextItem'));
    }

    // =========================================================================
    // BAGIAN 2: ADMIN SIDE (Manajemen Folder & File)
    // =========================================================================

    // Menampilkan daftar item (File Manager)
    public function index(Request $request)
    {
        $parentId = $request->query('parent_id');
        
        // Ambil item berdasarkan parent saat ini
        $items = LmsItem::where('parent_id', $parentId)
                    ->orderBy('type', 'asc') // Folder di atas
                    ->orderBy('order', 'asc')
                    ->get();

        // Ambil data Parent untuk Breadcrumb
        $currentFolder = $parentId ? LmsItem::find($parentId) : null;

        return view('admin.lms.index', compact('items', 'currentFolder'));
    }

    // Menampilkan Form Tambah
    public function create(Request $request)
    {
        $parentId = $request->query('parent_id');
        $parent = $parentId ? LmsItem::find($parentId) : null;
        
        return view('admin.lms.create', compact('parentId', 'parent'));
    }

    // Proses Simpan Data
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type'  => 'required',
            'cover_image' => 'image|mimes:jpeg,png,jpg|max:2048|nullable',
            'file_upload' => 'required_if:type,file|mimes:pdf|max:10000', 
            'url_link'    => 'required_if:type,video,link',
        ]);

        $data = [
            'parent_id' => $request->parent_id,
            'title'     => $request->title,
            // Buat slug unik agar tidak error jika ada judul sama
            'slug'      => Str::slug($request->title) . '-' . Str::random(5), 
            'type'      => $request->type,
            'is_active' => true,
        ];

        // 1. Upload Cover Image
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_cover_' . $file->getClientOriginalName();
            $file->move(public_path('assets/uploads'), $filename);
            $data['cover_image'] = 'assets/uploads/' . $filename;
        }

        // 2. Handle Konten
        if ($request->type == 'file') {
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/pdf'), $filename);
                $data['content'] = 'assets/pdf/' . $filename;
            }
        } 
        elseif ($request->type == 'video') {
            // Auto Convert Youtube Watch -> Embed
            $url = $request->url_link;
            $url = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "https://www.youtube.com/embed/$2", $url);
            $data['content'] = $url;
        }
        elseif ($request->type == 'link') {
            $data['content'] = $request->url_link;
        }

        LmsItem::create($data);

        return redirect()->route('lms-items.index', ['parent_id' => $request->parent_id])
                         ->with('success', 'Item berhasil ditambahkan');
    }

    // Hapus Data
    public function destroy($id)
    {
        $item = LmsItem::findOrFail($id);
        $parentId = $item->parent_id;
        
        // Hapus file fisik jika ada
        if ($item->type == 'file' && $item->content && file_exists(public_path($item->content))) {
            unlink(public_path($item->content));
        }

        $item->delete(); 

        return redirect()->route('lms-items.index', ['parent_id' => $parentId])
                         ->with('success', 'Item berhasil dihapus');
    }

    // =========================================================================
    // BAGIAN 3: ANALYTICS (Agar route admin.analytics tidak error)
    // =========================================================================
    public function analytics()
    {
        if (Auth::user()->role === 'siswa') {
        abort(403, 'Maaf, halaman ini hanya untuk Guru dan Admin.');
    }
        // --- 1. TOTAL AKSES (Filter: Hanya Siswa) ---
        $totalViews = LmsAccessLog::whereHas('user', function($q) {
            $q->where('role', 'siswa');
        })->count();
        
        // --- 2. SISWA AKTIF (Filter: Hanya Siswa) ---
        $activeStudents = LmsAccessLog::whereHas('user', function($q) {
            $q->where('role', 'siswa');
        })->distinct('user_id')->count('user_id');

        // --- 3. MATERI TERPOPULER (Filter: Hanya akses oleh Siswa) ---
        // Kita juga filter ini agar sinkron dengan total views
        $topMaterials = LmsAccessLog::select('lms_item_id', DB::raw('count(*) as total'))
                        ->whereHas('user', function($q) {
                            $q->where('role', 'siswa');
                        })
                        ->groupBy('lms_item_id')
                        ->orderByDesc('total')
                        ->with('lmsItem')
                        ->take(5)
                        ->get();

        // --- 4. DATA GRAFIK 7 HARI (Filter: Hanya Siswa) ---
        $chartData = LmsAccessLog::select(
                            DB::raw('DATE(created_at) as date'), 
                            DB::raw('count(*) as total')
                        )
                        ->whereHas('user', function($q) {
                            $q->where('role', 'siswa');
                        })
                        ->where('created_at', '>=', now()->subDays(7))
                        ->groupBy('date')
                        ->orderBy('date', 'asc')
                        ->get();
        
        $chartLabels = $chartData->pluck('date');
        $chartValues = $chartData->pluck('total');

        // --- 5. RIWAYAT AKSES TERBARU (Filter: Hanya Siswa) ---
        $recentLogs = LmsAccessLog::with(['user', 'lmsItem'])
                    ->whereHas('user', function($q) {
                        $q->where('role', 'siswa');
                    })
                    ->latest()
                    ->take(10)
                    ->get();

        return view('admin.lms.analytics', compact(
            'recentLogs', 'totalViews', 'activeStudents', 'topMaterials', 'chartLabels', 'chartValues'
        ));
    }    
        // --- ADMIN: HALAMAN EDIT ---
    public function edit($id)
    {
        $item = LmsItem::findOrFail($id);
        return view('admin.lms.edit', compact('item'));
    }

    // --- ADMIN: PROSES UPDATE ---
    public function update(Request $request, $id)
    {
        $item = LmsItem::findOrFail($id);

        $request->validate([
            'title' => 'required',
            // Cover dan File bersifat nullable (opsional) saat update
            // karena user mungkin tidak ingin menggantinya
            'cover_image' => 'image|mimes:jpeg,png,jpg|max:2048|nullable',
            'file_upload' => 'mimes:pdf|max:10000|nullable',
        ]);

        // 1. Update Data Dasar
        $item->title = $request->title;
        
        // Catatan: Tipe (Folder/File/Video) sebaiknya tidak diubah sembarangan 
        // karena akan merusak struktur data kontennya. 
        // Jadi kita hanya update kontennya saja sesuai tipe awal.

        // 2. Cek Apakah Ada Upload Cover Baru?
        if ($request->hasFile('cover_image')) {
            // Hapus cover lama jika ada & bukan default
            if ($item->cover_image && file_exists(public_path($item->cover_image))) {
                unlink(public_path($item->cover_image));
            }

            // Upload baru
            $file = $request->file('cover_image');
            $filename = time() . '_cover_' . $file->getClientOriginalName();
            $file->move(public_path('assets/uploads'), $filename);
            $item->cover_image = 'assets/uploads/' . $filename;
        }

        // 3. Update Konten (Hanya jika user menginput data baru)
        if ($item->type == 'file') {
            // Jika ada file PDF baru diupload
            if ($request->hasFile('file_upload')) {
                // Hapus file lama
                if ($item->content && file_exists(public_path($item->content))) {
                    unlink(public_path($item->content));
                }
                
                // Upload baru
                $file = $request->file('file_upload');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/pdf'), $filename);
                $item->content = 'assets/pdf/' . $filename;
            }
        } 
        elseif ($item->type == 'video') {
            // Jika URL Video diubah
            if ($request->filled('url_link')) {
                $url = $request->url_link;
                $url = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "https://www.youtube.com/embed/$2", $url);
                $item->content = $url;
            }
        }
        elseif ($item->type == 'link') {
            if ($request->filled('url_link')) {
                $item->content = $request->url_link;
            }
        }

        $item->save();

        return redirect()->route('lms-items.index', ['parent_id' => $item->parent_id])
                         ->with('success', 'Item berhasil diperbarui');
    }
public function getStudents(Request $request)
    {
        $search = $request->query('search');
        
        $students = \App\Models\User::where('role', 'siswa') // Syarat Mutlak 1: Harus Siswa
            ->when($search, function($query) use ($search) {
                // Syarat 2: (Nama MIRIP search ATAU Email MIRIP search)
                // Kita bungkus dalam function($q) agar menjadi tanda kurung di SQL
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(5); 

        return view('admin.lms.partials.student_list', compact('students'))->render();
    }
    // --- BARU: AJAX UNTUK DETAIL HISTORY SISWA ---
    public function getStudentHistory($id)
    {
        $student = \App\Models\User::findOrFail($id);
        
        // Ambil riwayat akses siswa ini
        $history = LmsAccessLog::with('lmsItem')
                    ->where('user_id', $id)
                    ->latest()
                    ->limit(50) // Batasi 50 terakhir biar tidak kepanjangan
                    ->get();

        return response()->json([
            'student_name' => $student->name,
            'history' => $history
        ]);
    }
}