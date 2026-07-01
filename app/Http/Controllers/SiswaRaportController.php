<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\RaportFile;
use App\Models\RaportCategory;
use App\Models\Kelas;

class SiswaRaportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');

        // Ambil Riwayat Kelas
        $riwayatKelas = $siswa->riwayatKelas()
                                ->with(['waliKelas', 'tahunAjaran'])
                                ->get()
                                ->sortByDesc(function($kelas) {
                                    return $kelas->pivot->tahun_ajaran_id;
                                });
        
        // HAPUS BAGIAN REDIRECT OTOMATIS INI:
        // if ($kelasTerbaru) { return redirect(...) } 

        return view('siswa.raport.index', compact('riwayatKelas'));
    }

    public function show(Request $request, $kelas_id, $semester = null)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        // 1. Validasi Akses Kelas (Apakah siswa benar-benar pernah di kelas ini?)
        $isMember = \Illuminate\Support\Facades\DB::table('kelas_siswa')
                        ->where('siswa_id', $siswa->id)
                        ->where('kelas_id', $kelas_id)
                        ->exists();

        if (!$isMember) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $kelas = Kelas::with(['tahunAjaran', 'waliKelas'])->findOrFail($kelas_id);

        // 2. LOGIC SIDEBAR (Kategori Induk)
        $rootCategories = RaportCategory::whereNull('parent_id')
                            ->with('children')
                            ->orderBy('type', 'desc') // Folder dulu baru FileType
                            ->orderBy('id', 'asc')
                            ->get();

        // 3. LOGIC FOLDER AKTIF (Sama seperti Guru Controller)
        // Ambil Folder ID dari URL (?folder_id=...), jika tidak ada ambil Folder Pertama
        $firstCategory = $rootCategories->first();
        $defaultId = $firstCategory ? $firstCategory->id : null;
        $selectedFolderId = $request->query('folder_id', $defaultId);
        
        $selectedFolder = RaportCategory::with('children')->find($selectedFolderId);

        // Jika kategori dihapus admin tapi user masih akses link lama
        if (!$selectedFolder) {
            return redirect()->route('siswa.raport.show', ['kelas_id' => $kelas_id]);
        }

        // 4. Kumpulkan ID Kategori yang Relevan (Induk + Anak-anaknya)
        // Agar jika user klik Folder "UTS", file di dalam sub-kategori "UTS Matematika" juga muncul
        $relevantCategoryIds = [$selectedFolderId];
        
        if ($selectedFolder->type == 'folder') {
             $relevantCategoryIds = array_merge($relevantCategoryIds, $selectedFolder->children->pluck('id')->toArray());
        }

        // 5. AMBIL FILE RAPORT
        // Filter berdasarkan: Siswa Login, Kelas Dipilih, dan Kategori Relevan
        $files = RaportFile::where('student_id', $user->id)
                           ->where('kelas_id', $kelas_id)
                           ->whereIn('raport_category_id', $relevantCategoryIds)
                           ->with('category') // Eager load nama kategori
                           ->orderBy('created_at', 'desc')
                           ->get();

        // 6. Data Tambahan untuk Navigasi (Riwayat Kelas di Sidebar/Dropdown)
        $riwayatKelas = $siswa->riwayatKelas()
                              ->with(['tahunAjaran'])
                              ->get()
                              ->sortByDesc(function($k) {
                                  return $k->pivot->tahun_ajaran_id;
                              });

        return view('siswa.raport.show', compact(
            'siswa',
            'kelas',
            'riwayatKelas',
            'rootCategories',
            'selectedFolder',
            'files'
        ));
    }
}