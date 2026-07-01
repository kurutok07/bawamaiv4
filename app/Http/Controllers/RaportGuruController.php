<?php

namespace App\Http\Controllers;

use App\Models\Siswa; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Kelas;
use App\Models\RaportCategory;
use App\Models\RaportFile;
use App\Models\TahunAjaran;
use App\Models\Guru; 

class RaportGuruController extends Controller
{
    // 1. INDEX (PILIH KELAS) -- TIDAK BERUBAH --
    public function index()
    {
        $activeYear = \App\Models\TahunAjaran::where('is_active', 1)->first();
        if (!$activeYear) return back()->with('error', 'Belum ada Tahun Ajaran yang aktif.');

        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
        if (!$guru) return back()->with('error', 'Akun belum terhubung ke Data Guru.');

        $classes = \App\Models\Kelas::where('wali_kelas_id', $guru->id)
                                    ->where('tahun_ajaran_id', $activeYear->id)
                                    ->withCount('siswas') 
                                    ->get();
                                    
        return view('guru.raport.index', compact('classes', 'activeYear'));
    }

    // 2. SHOW (DETAIL & LIST FILE)
public function show(Request $request, $kelas_id)
    {
        $kelas = \App\Models\Kelas::with(['siswas', 'tahunAjaran'])->findOrFail($kelas_id);
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();

        if (!$guru || $kelas->wali_kelas_id != $guru->id) {
            abort(403, 'Akses Ditolak.');
        }
        
        // 1. Ambil Root Categories
        $rootCategories = \App\Models\RaportCategory::whereNull('parent_id')
                                        ->with('children')
                                        ->orderBy('type', 'desc') // Folder dulu
                                        ->orderBy('id', 'asc')
                                        ->get();

        // --- PERBAIKAN ERROR "ID on Null" DIMULAI DISINI ---
        
        // Cek dulu: Apakah ada kategori?
        $firstCategory = $rootCategories->first();
        
        // Kalau kosong (belum ada kategori sama sekali), set 0 atau null
        $defaultId = $firstCategory ? $firstCategory->id : null;

        // Ambil ID dari URL, kalau gak ada pake defaultId tadi
        $selectedFolderId = $request->query('folder_id', $defaultId);

        // Cari object foldernya
        $selectedFolder = $rootCategories->where('id', $selectedFolderId)->first();

        // Jika Selected Folder KOSONG (Misal: User hapus kategori yang sedang dibuka),
        // Maka kita kembalikan collection kosong biar view gak error.
        if (!$selectedFolder) {
             return view('guru.raport.show', [
                'kelas' => $kelas,
                'rootCategories' => $rootCategories,
                'selectedFolder' => null, // Kirim null
                'files' => collect([]),   // Kirim collection kosong
            ])->with('warning', 'Belum ada kategori/folder raport yang dibuat Admin.');
        }

        // --- LANJUT LOGIC NORMAL ---

        // Kumpulkan ID (Diri sendiri + Anak-anaknya)
        $relevantCategoryIds = [$selectedFolderId];
        // Cek children cuma kalau dia Folder
        if ($selectedFolder->type == 'folder') {
             $relevantCategoryIds = array_merge($relevantCategoryIds, $selectedFolder->children->pluck('id')->toArray());
        }

        $files = \App\Models\RaportFile::where('kelas_id', $kelas->id)
                           ->whereIn('raport_category_id', $relevantCategoryIds)
                           ->get()
                           ->groupBy('student_id'); 

        return view('guru.raport.show', compact(
            'kelas', 
            'rootCategories', 
            'selectedFolder', 
            'files'
        ));
    }
    
    // 3. UPLOAD FILE (STORE)
public function store(Request $request)
{
    $request->validate([
        'student_id'         => 'required',
        'kelas_id'           => 'required',
        'raport_category_id' => 'required',
        // Validasi Array: 'file_raport.*' artinya setiap file dicek satu per satu
        'file_raport'        => 'required',
        'file_raport.*'      => 'mimes:pdf|max:5120', // Max 5MB per file biar aman
    ]);

    $siswa = \App\Models\Siswa::findOrFail($request->student_id);
    $targetUserId = $siswa->user_id;
    $kelas = \App\Models\Kelas::findOrFail($request->kelas_id);
    
    // Ambil semua file yang diupload (Array)
    if ($request->hasFile('file_raport')) {
        
        $files = $request->file('file_raport');

        foreach ($files as $file) {
            // 1. Ambil Nama Asli File (Tanpa Ekstensi .pdf biar rapi, opsional)
            // Contoh: "Sertifikat Lomba.pdf" -> Disimpan "Sertifikat Lomba"
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // 2. Upload File
            $path = $file->store('raport_files', 'public');

            // 3. Simpan ke Database (Selalu Create Baru / Append)
            // Kita tidak pakai updateOrCreate karena 1 siswa bisa punya banyak file dengan kategori sama
            \App\Models\RaportFile::create([
                'student_id'         => $targetUserId,
                'kelas_id'           => $request->kelas_id,
                'raport_category_id' => $request->raport_category_id,
                'nama_file'          => $originalName, // Pakai nama asli file
                'tahun_ajaran_id'    => $kelas->tahun_ajaran_id,
                'uploaded_by'        => Auth::id(),
                'file_path'          => $path
            ]);
        }
    }

    return back()->with('success', 'Berhasil mengupload ' . count($files) . ' dokumen.');
}


    // 4. DESTROY (HAPUS) -- TIDAK BERUBAH BANYAK --
    public function destroy($id)
    {
        $raport = RaportFile::findOrFail($id);

        if($raport->uploaded_by != Auth::id()) abort(403);

        if(Storage::disk('public')->exists($raport->file_path)) {
            Storage::disk('public')->delete($raport->file_path);
        }

        $raport->delete();
        return back()->with('success', 'File berhasil dihapus.');
    }
}