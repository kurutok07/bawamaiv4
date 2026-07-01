<?php

namespace App\Http\Controllers;

use App\Models\LmsItem;
use App\Models\LmsAccessLog;
use App\Models\Kelas; // <--- Import Kelas
use App\Models\Guru;  // <--- Import Guru
use App\Models\TahunAjaran; // <--- Import TA
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LmsController extends Controller
{
    // --- HELPER: Cek Guru Login & TA Aktif ---
    private function getLoggedGuru()
    {
        if (Auth::user()->role === 'guru') {
            return Guru::where('user_id', Auth::id())->first();
        }
        return null;
    }

    private function getActiveTahunAjaran()
    {
        return TahunAjaran::where('is_active', 1)->first();
    }

    // =========================================================================
    // BAGIAN 1: USER / SISWA SIDE (Menampilkan Materi)
    // =========================================================================

    public function show($slug)
    {
        // 1. Cari item saat ini
        $item = LmsItem::where('slug', $slug)->firstOrFail();

        // --- SECURITY CHECK (BARU) ---
        // Jika materi ini KHUSUS KELAS, cek apakah siswa berhak akses?
        if (Auth::user()->role === 'siswa' && $item->kelas_id) {
            $activeTa = $this->getActiveTahunAjaran();
            
            // Cek apakah siswa login terdaftar di kelas target materi ini?
            $isAuthorized = \App\Models\Siswa::where('user_id', Auth::id())
                ->whereHas('kelas', function($q) use ($item, $activeTa) {
                    $q->where('kelas.id', $item->kelas_id)
                      ->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
                })->exists();

            if (!$isAuthorized) {
                // Tampilkan pesan error jika siswa mencoba akses materi kelas lain
                abort(403, 'Akses Ditolak. Materi ini khusus untuk kelas ' . ($item->kelas->nama_kelas ?? '-'));
            }
        }
        // -----------------------------

        // 2. Analytics
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

        // 3. Navigasi Previous & Next
        $prevItem = null;
        $nextItem = null;

        if ($item->parent_id) {
            $siblings = LmsItem::where('parent_id', $item->parent_id)
                               ->where('is_active', true)
                               ->orderBy('order', 'asc')
                               ->orderBy('id', 'asc')
                               ->get();

            $currentIndex = $siblings->search(function($sibling) use ($item) {
                return $sibling->id === $item->id;
            });

            $prevItem = $siblings->get($currentIndex - 1);
            $nextItem = $siblings->get($currentIndex + 1);
        }

        // 4. Tentukan View
        if ($item->type === 'folder') {
            $children = $item->children()->where('is_active', true)->get();
            return view('lms.folder', compact('item', 'children'));
        } 
        
        return view('lms.content', compact('item', 'prevItem', 'nextItem'));
    }

    // =========================================================================
    // BAGIAN 2: ADMIN & GURU SIDE (Manajemen Folder & File)
    // =========================================================================

    // Menampilkan daftar item (File Manager)
// Menampilkan daftar item (File Manager)
    public function index(Request $request)
    {
        $parentId = $request->query('parent_id');
        
        // Query Dasar
        $query = LmsItem::where('parent_id', $parentId);

        // --- FILTER KHUSUS GURU ---
        if (Auth::user()->role === 'guru') {
            $guru = $this->getLoggedGuru();
            if ($guru) {
                $activeTa = $this->getActiveTahunAjaran();

                $query->where('guru_id', $guru->id)
                      ->where(function($q) use ($activeTa) {
                          // 1. Tampilkan file yang terikat dengan kelas pada Tahun Ajaran Aktif
                          $q->whereHas('kelas', function($queryKelas) use ($activeTa) {
                              $queryKelas->where('tahun_ajaran_id', $activeTa->id);
                          })
                          // 2. Tetap tampilkan Folder Umum / Root (yang kelas_id-nya null)
                          // agar hirarki folder guru tidak rusak, tapi isinya nanti akan kosong.
                          ->orWhereNull('kelas_id'); 
                      });
            }
        } elseif (in_array(Auth::user()->role, ['perpus', 'admin_qurana'])) {
    // 1. HARAM melihat LMS Kelas
    $query->whereNull('kelas_id');
    
    // 2. Hanya melihat folder dan file miliknya sendiri
    $query->where('user_id', Auth::id());
}
                // ---------------------------------

        $items = $query->orderBy('type', 'asc') // Folder di atas
                       ->orderBy('order', 'asc')
                       ->get();

        $currentFolder = $parentId ? LmsItem::find($parentId) : null;

        return view('admin.lms.index', compact('items', 'currentFolder'));
    }

    // Menampilkan Form Tambah
    public function create(Request $request)
    {
        $parentId = $request->query('parent_id');
        $parent = $parentId ? LmsItem::find($parentId) : null;
        
        // --- DATA UNTUK DROPDOWN KELAS (BARU) ---
        $activeTa = $this->getActiveTahunAjaran();
        $daftarKelas = [];

        if (Auth::user()->role === 'admin') {
            // Admin bisa pilih semua kelas
            $daftarKelas = Kelas::where('tahun_ajaran_id', $activeTa->id)->get();
        } elseif (Auth::user()->role === 'guru') {
            // Guru hanya bisa pilih kelas yang diajar
            $guru = $this->getLoggedGuru();
            if ($guru) {
                $daftarKelas = $guru->kelasAjar()
                                    ->wherePivot('tahun_ajaran_id', $activeTa->id)
                                    ->get();
            }
        }

        return view('admin.lms.create', compact('parentId', 'parent', 'daftarKelas'));
    }

    // Proses Simpan Data
// Proses Simpan Data
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type'  => 'required',
            'cover_image' => 'image|mimes:jpeg,png,jpg|max:2048|nullable',
            'file_upload' => 'required_if:type,file|mimes:pdf|max:10000',
            'url_link'    => 'required_if:type,video,link',
            'kelas_id'    => 'nullable|exists:kelas,id',
        ]);

        // Cek ID Guru Pengupload
        $guruId = null;
        if (Auth::user()->role === 'guru') {
            $guru = $this->getLoggedGuru();
            $guruId = $guru ? $guru->id : null;
        }

        $data = [
            'parent_id' => $request->parent_id,
            'title'     => $request->title,
            'slug'      => Str::slug($request->title) . '-' . Str::random(5),
            'type'      => $request->type,
            'is_active' => true,
            'kelas_id'  => $request->kelas_id,
            'guru_id'   => $guruId,
            'user_id'   => Auth::id(),
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
            $url = $request->url_link;
            
            // --- FIX REGEX YOUTUBE (SUPPORT SHORTS) ---
            // Menambahkan '|shorts' di dalam pola agar link shorts terbaca
            $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
            
            if (preg_match($pattern, $url, $matches)) {
                $videoId = $matches[1];
                // Paksa jadi link embed biasa agar bisa diputar di iframe
                $data['content'] = 'https://www.youtube.com/embed/' . $videoId;
            } else {
                $data['content'] = $url; 
            }
        }
        elseif ($request->type == 'link') {
            $data['content'] = $request->url_link;
        }

        LmsItem::create($data);

        return redirect()->route('lms-items.index', ['parent_id' => $request->parent_id])
                         ->with('success', 'Item berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = LmsItem::findOrFail($id);
        
        // Reuse logic daftar kelas utk dropdown edit
        $activeTa = $this->getActiveTahunAjaran();
        $daftarKelas = [];

        if (Auth::user()->role === 'admin') {
            $daftarKelas = Kelas::where('tahun_ajaran_id', $activeTa->id)->get();
        } elseif (Auth::user()->role === 'guru') {
            $guru = $this->getLoggedGuru();
            if ($guru) {
                $daftarKelas = $guru->kelasAjar()
                                    ->wherePivot('tahun_ajaran_id', $activeTa->id)
                                    ->get();
            }
        }

        return view('admin.lms.edit', compact('item', 'daftarKelas'));
    }

    // Update Data
// Update Data
    public function update(Request $request, $id)
    {
        $item = LmsItem::findOrFail($id);

        $request->validate([
            'title'       => 'required',
            'cover_image' => 'image|mimes:jpeg,png,jpg|max:2048|nullable',
            'file_upload' => 'mimes:pdf|max:10000|nullable',
            'kelas_id'    => 'nullable|exists:kelas,id',
        ]);

        $item->title    = $request->title;
        $item->kelas_id = $request->kelas_id;

        // Handle Cover Image
        if ($request->hasFile('cover_image')) {
            if ($item->cover_image && file_exists(public_path($item->cover_image))) {
                unlink(public_path($item->cover_image));
            }
            $file = $request->file('cover_image');
            $filename = time() . '_cover_' . $file->getClientOriginalName();
            $file->move(public_path('assets/uploads'), $filename);
            $item->cover_image = 'assets/uploads/' . $filename;
        }

        // Handle Content Update
        if ($item->type == 'file') {
            if ($request->hasFile('file_upload')) {
                if ($item->content && file_exists(public_path($item->content))) {
                    unlink(public_path($item->content));
                }
                $file = $request->file('file_upload');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/pdf'), $filename);
                $item->content = 'assets/pdf/' . $filename;
            }
        }
	elseif ($item->type == 'video') {
            if ($request->filled('url_link')) {
                $url = $request->url_link;
                
                // --- FIX REGEX YOUTUBE (SUPPORT SHORTS) ---
                $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';

                if (preg_match($pattern, $url, $matches)) {
                    $videoId = $matches[1];
                    $item->content = 'https://www.youtube.com/embed/' . $videoId;
                } else {
                    $item->content = $url;
                }
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

    public function destroy($id)
    {
        $item = LmsItem::findOrFail($id);
        $parentId = $item->parent_id;
        
        if ($item->type == 'file' && $item->content && file_exists(public_path($item->content))) {
            unlink(public_path($item->content));
        }

        $item->delete(); 

        return redirect()->route('lms-items.index', ['parent_id' => $parentId])
                         ->with('success', 'Item berhasil dihapus');
    }

    // =========================================================================
    // BAGIAN 3: ANALYTICS (SISWA)
    // =========================================================================
public function analytics()
    {
        if (Auth::user()->role === 'siswa') {
            abort(403, 'Maaf, halaman ini hanya untuk Guru dan Admin.');
        }

        // --- 1. MEMBUAT QUERY DASAR (BASE QUERY) ---
        // Kita buat query dasar dulu, nanti ditempel filter guru jika perlu.
        $query = LmsAccessLog::query();

        // Filter Wajib: Hanya log milik Siswa
        $query->whereHas('user', function($q) {
            $q->where('role', 'siswa');
        });

        // --- 2. FILTER KHUSUS GURU ---
        // Jika Guru, hanya ambil log dari:
        // a. Materi yang diupload guru tersebut (guru_id)
        // b. ATAU Materi yang ditujukan untuk kelas ajar guru tersebut (kelas_id)
        if (Auth::user()->role === 'guru') {
            $guru = $this->getLoggedGuru();
            
            if ($guru) {
                $activeTa = $this->getActiveTahunAjaran();
                $kelasAjarIds = $guru->kelasAjar()
                                     ->wherePivot('tahun_ajaran_id', $activeTa->id)
                                     ->pluck('kelas.id');

                // Terapkan Filter ke Query Log
                $query->whereHas('lmsItem', function($q) use ($guru, $kelasAjarIds) {
                    $q->where('guru_id', $guru->id)          // Materi milik guru
                      ->orWhereIn('kelas_id', $kelasAjarIds); // Materi kelas ajar
                });
            }
        } 
        // --- 3. EKSEKUSI DATA (MENGGUNAKAN QUERY YANG SUDAH DIFILTER) ---

        // A. Total Views
        // (Clone query agar tidak merusak query dasar untuk hitungan berikutnya)
        $totalViews = (clone $query)->count();

        // B. Siswa Aktif (Unik)
        $activeStudents = (clone $query)->distinct('user_id')->count('user_id');

        // C. Materi Terpopuler (Top 5)
        $topMaterials = (clone $query)
            ->select('lms_item_id', DB::raw('count(*) as total'))
            ->groupBy('lms_item_id')
            ->orderByDesc('total')
            ->with('lmsItem')
            ->take(5)
            ->get();

        // D. Data Grafik (7 Hari Terakhir)
        $chartData = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        $chartLabels = $chartData->pluck('date');
        $chartValues = $chartData->pluck('total');

        // E. Riwayat Terbaru (Tabel)
        $recentLogs = (clone $query)
            ->with(['user', 'lmsItem'])
            ->latest()
            ->take(10)
            ->get();
        $availableClasses = [];
        $activeTa = $this->getActiveTahunAjaran();

        if (Auth::user()->role === 'admin') {
            // Admin: Ambil SEMUA kelas di TA aktif
            $availableClasses = \App\Models\Kelas::where('tahun_ajaran_id', $activeTa->id)
                                ->orderBy('tingkat')
                                ->orderBy('nama_kelas')
                                ->get();
        } 
        elseif (Auth::user()->role === 'guru') {
            // Guru: Hanya ambil kelas yang DIAJAR
            $guru = $this->getLoggedGuru();
            if ($guru) {
                $availableClasses = $guru->kelasAjar()
                                         ->wherePivot('tahun_ajaran_id', $activeTa->id)
                                         ->orderBy('tingkat')
                                         ->orderBy('nama_kelas')
                                         ->get();
            }
        }

        // Jangan lupa tambahkan 'availableClasses' ke compact
        return view('admin.lms.analytics', compact(
            'recentLogs', 'totalViews', 'activeStudents', 'topMaterials', 'chartLabels', 'chartValues',
            'availableClasses' // <--- KIRIM INI
        ));
    }

public function getStudents(Request $request)
    {
        $search = $request->query('search');
        $kelasId = $request->query('kelas_id'); // <--- Tangkap Filter Kelas
        
        $query = \App\Models\User::where('role', 'siswa');

        $activeTa = $this->getActiveTahunAjaran();

        // --- 1. FILTER BERDASARKAN KELAS YANG DIPILIH (DROPDOWN) ---
        if ($kelasId) {
            $query->whereHas('siswa', function($q) use ($kelasId, $activeTa) {
                $q->whereHas('riwayatKelas', function($q2) use ($kelasId, $activeTa) {
                    $q2->where('kelas.id', $kelasId)
                       ->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
                });
            });
        }

        // --- 2. SECURITY CHECK UNTUK GURU ---
        // Walaupun dropdown sudah difilter, kita harus pastikan Guru 
        // tidak bisa "menembak" ID kelas lain lewat Inspect Element/URL.
        if (Auth::user()->role === 'guru') {
            $guru = $this->getLoggedGuru();
            if ($guru) {
                $kelasAjarIds = $guru->kelasAjar()
                                     ->wherePivot('tahun_ajaran_id', $activeTa->id)
                                     ->pluck('kelas.id')
                                     ->toArray();

                // Jika Guru memilih kelas, pastikan ID-nya ada di daftar ajarnya
                if ($kelasId && !in_array($kelasId, $kelasAjarIds)) {
                    // Jika mencoba akses kelas orang lain, kosongkan hasil
                    return response()->json(['html' => '<div class="text-danger text-center p-3">Akses Ditolak: Kelas tidak ditemukan.</div>']);
                }

                // Jika Guru memilih "Semua Kelas" (Value Kosong),
                // Kita tetap harus batasi querynya hanya ke lingkup kelas ajarnya saja
                if (!$kelasId) {
                    $query->whereHas('siswa', function($q) use ($kelasAjarIds, $activeTa) {
                        $q->whereHas('riwayatKelas', function($q2) use ($kelasAjarIds, $activeTa) {
                            $q2->whereIn('kelas.id', $kelasAjarIds)
                               ->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
                        });
                    });
                }
            }
        }
        // -----------------------------------------------------------

        // Filter Pencarian Nama
        $query->when($search, function($q) use ($search) {
            $q->where(function($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });

        $students = $query->latest()->paginate(5); 

        return view('admin.lms.partials.student_list', compact('students'))->render();
    }    
    
public function getStudentHistory($id)
    {
        $student = \App\Models\User::findOrFail($id);
        
        $query = LmsAccessLog::with(['lmsItem.parent']) // <--- PENTING: Load Parent
                             ->where('user_id', $id);

        // Filter Guru (Logic Lama Tetap Dipakai)
        if (Auth::user()->role === 'guru') {
            $guru = $this->getLoggedGuru();
            if ($guru) {
                $activeTa = $this->getActiveTahunAjaran();
                $kelasAjarIds = $guru->kelasAjar()->wherePivot('tahun_ajaran_id', $activeTa->id)->pluck('kelas.id');

                $query->whereHas('lmsItem', function($q) use ($guru, $kelasAjarIds) {
                    $q->where('guru_id', $guru->id)
                      ->orWhereIn('kelas_id', $kelasAjarIds);
                });
            }
        }

        $history = $query->latest()->limit(50)->get();

        return response()->json(['student_name' => $student->name, 'history' => $history]);
    }
    // --- AJAX: DETAIL SISWA UNTUK MODAL ---
public function getStudentDetail($id)
    {
        $siswa = \App\Models\Siswa::with('kelas')->where('user_id', $id)->firstOrFail();
        $activeTa = \App\Models\TahunAjaran::where('is_active', 1)->first();
        $kelasAktif = null;
        
        if ($activeTa) {
            $kelasAktif = $siswa->kelas()
                                ->wherePivot('tahun_ajaran_id', $activeTa->id)
                                ->first();
        }

        $logs = \App\Models\LmsAccessLog::with(['lmsItem.parent'])
                    ->where('user_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function($log) {
                        $itemTitle = $log->lmsItem ? $log->lmsItem->title : '<span class="text-danger font-italic">Item dihapus</span>';
                        $itemType  = $log->lmsItem ? $log->lmsItem->type : '-';
                        
                        $folder = 'Umum/Root';
                        if ($log->lmsItem && $log->lmsItem->parent) {
                            $folder = $log->lmsItem->parent->title;
                        }

                        return [
                            'item_title' => $itemTitle,
                            'item_type'  => $itemType,
                            'folder'     => $folder,
                            // FIX TIMEZONE HERE
                            'time'       => $log->created_at->setTimezone('Asia/Pontianak')->format('d M Y H:i'),
                            'device'     => (str_contains($log->user_agent, 'Mobile') || str_contains($log->user_agent, 'Android')) ? 'HP/Tablet' : 'PC/Laptop'
                        ];
                    });

        $totalAkses = \App\Models\LmsAccessLog::where('user_id', $id)->count();

        return response()->json([
            'siswa' => $siswa,
            'stats' => [
                'kelas' => $kelasAktif ? $kelasAktif->nama_kelas : 'Belum Masuk Kelas',
                'wali'  => $siswa->nama_wali ?? '-',
                'total_akses' => $totalAkses
            ],
            'logs' => $logs
        ]);
    }
    
// =========================================================================
    // BAGIAN 4: ANALYTICS GURU (FITUR BARU - UPDATED)
    // =========================================================================
    
    public function teacherAnalytics()
    {
        // 1. Cek Permission (Admin & Kepsek Only)
        if (!in_array(Auth::user()->role, ['admin', 'kepala_sekolah'])) {
            abort(403, 'Akses Ditolak');
        }

        // 2. Ambil Data Semua Guru + Hitung Total Upload Materi
        $gurus = \App\Models\Guru::with('user')
            ->withCount(['lmsItems as total_materi' => function($q) {
                // Hanya hitung file/video/link, bukan folder kosong
                $q->where('type', '!=', 'folder'); 
            }])
            ->get()
            ->map(function($guru) {
                // Hitung Total Login (Manual Query ke tabel logs)
                $guru->total_login = DB::table('login_logs')
                                       ->where('user_id', $guru->user_id)
                                       ->count();
                return $guru;
            })
            ->sortByDesc('total_materi'); // Urutkan dari yang paling rajin

        // 3. Data untuk Grafik (Top 5 Pengupload)
        // Ambil 5 teratas, lalu pluck nama lengkapnya
        $top5 = $gurus->take(5);
        $topGuruNames = $top5->map(function($g) {
            // Format nama pendek untuk grafik
            return Str::limit($g->nama_lengkap, 15); 
        })->values(); // values() untuk reset index array jadi [0,1,2..] agar chartjs tidak bingung
        
        $topGuruCounts = $top5->pluck('total_materi')->values();

        // 4. Recent Activity Feed (Gabungan Login & Upload)
        
        // A. Ambil 10 Login Terakhir (Join ke tabel Guru untuk dapat Nama & Gelar)
        $recentLogins = DB::table('login_logs')
            ->join('users', 'login_logs.user_id', '=', 'users.id')
            ->join('gurus', 'users.id', '=', 'gurus.user_id') 
            ->select(
                'gurus.nama_lengkap', 
                'gurus.gelar_depan', 
                'gurus.gelar_belakang',
                'login_logs.login_at as created_at', 
                DB::raw("'login' as type"), 
                DB::raw("NULL as item_title")
            )
            ->orderBy('login_logs.id', 'desc')
            ->limit(10)
            ->get();

        // B. Ambil 10 Upload Terakhir (Eloquent Relationship)
        $recentUploads = \App\Models\LmsItem::with('guru')
            ->whereNotNull('guru_id')
            ->where('type', '!=', 'folder') // Bukan folder
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                // Mapping agar strukturnya sama dengan $recentLogins
                return (object) [
                    'nama_lengkap'   => $item->guru->nama_lengkap ?? 'Guru Terhapus',
                    'gelar_depan'    => $item->guru->gelar_depan ?? '',
                    'gelar_belakang' => $item->guru->gelar_belakang ?? '',
                    'created_at'     => $item->created_at,
                    'type'           => 'upload',
                    'item_title'     => $item->title
                ];
            });

        // C. Gabungkan Login & Upload, lalu Urutkan Waktu Terbaru
        $recentActivities = $recentLogins->merge($recentUploads)
                                         ->sortByDesc('created_at')
                                         ->take(10); // Ambil 10 aktivitas campuran terbaru

        return view('admin.lms.teacher_analytics', compact('gurus', 'topGuruNames', 'topGuruCounts', 'recentActivities'));
    }

    // --- AJAX DETAIL GURU (UNTUK MODAL) ---
    public function getTeacherDetail($id)
    {
        // 1. Ambil Data Guru
        $guru = \App\Models\Guru::with('user')->findOrFail($id);

        // 2. Ambil History Upload
        $uploads = \App\Models\LmsItem::where('guru_id', $id)
                    ->where('type', '!=', 'folder')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($item) {
                        return [
                            'title'      => $item->title,
                            'type'       => $item->type, // file, video, link
                            // Format Waktu Indonesia (WIB/WITA/WIT sesuai server, disini kita set Pontianak)
                            'created_at' => $item->created_at->setTimezone('Asia/Pontianak')->format('d M Y H:i'),
                            'audiens'    => $item->kelas ? $item->kelas->nama_kelas : 'Umum'
                        ];
                    });

        // 3. Ambil History Login (Max 50 terakhir)
        $logins = DB::table('login_logs')
                    ->where('user_id', $guru->user_id)
                    ->orderBy('login_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function($log) {
                        // Parsing Waktu Login
                        $loginTime = \Carbon\Carbon::parse($log->login_at)->setTimezone('Asia/Pontianak');
                        
                        // Deteksi Device Sederhana
                        $agent = strtolower($log->user_agent);
                        $device = 'PC/Laptop';
                        if (str_contains($agent, 'mobile') || str_contains($agent, 'android') || str_contains($agent, 'iphone')) {
                            $device = 'HP/Tablet';
                        }

                        return [
                            'ip'     => $log->ip_address,
                            'device' => $device,
                            'time'   => $loginTime->format('d M Y H:i'),
                            'ago'    => $loginTime->diffForHumans()
                        ];
                    });
        
        // 4. Statistik Ringkas
        $stats = [
            'total_upload' => $uploads->count(),
            'total_login'  => DB::table('login_logs')->where('user_id', $guru->user_id)->count()
        ];

        // 5. Return JSON ke View
        return response()->json([
            'guru'    => $guru,        // Objek guru lengkap (termasuk niy, gelar, hp, dll)
            'email'   => $guru->user->email ?? '-', // Email login
            'uploads' => $uploads,
            'logins'  => $logins,
            'stats'   => $stats
        ]);
    }    
    
}
