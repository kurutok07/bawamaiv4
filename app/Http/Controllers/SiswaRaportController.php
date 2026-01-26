<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\RaportFile; // <--- Import Model File
use App\Models\RaportCategory; // <--- Import Model Kategori

class SiswaRaportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // 1. Ambil Riwayat Kelas
        $riwayatKelas = $siswa->riwayatKelas()
                        ->with(['waliKelas'])
                        ->get()
                        ->sortByDesc(function($kelas) {
                            return $kelas->pivot->tahun_ajaran_id; 
                        });

        // 2. Ambil List Tahun Ajaran (Kamus ID => Nama)
        $listTahun = TahunAjaran::pluck('tahun', 'id');

        // 3. LOGIC BARU: Cek Ketersediaan File Raport
        // Ambil semua file milik siswa ini
$files = RaportFile::with('category')
                    ->where('student_id', $siswa->id)
                    ->get();

        $fileMap = [];
        
        foreach ($files as $file) {
            // Ambil nama kategori dan ID-nya
            $kategoriNama = strtolower($file->category->nama ?? '');
            $kategoriId   = $file->raport_category_id;
            
            $jenis = 'unknown';

            // CARA 1: Deteksi dari Nama (Prioritas Utama)
            if (str_contains($kategoriNama, 'ganjil')) {
                $jenis = 'ganjil';
            } elseif (str_contains($kategoriNama, 'genap')) {
                $jenis = 'genap';
            }

            // CARA 2: Deteksi dari ID (Fallback / Cadangan)
            // Jika Cara 1 gagal (hasil masih unknown), kita cek ID-nya
            // SILAHKAN SESUAIKAN ID INI DENGAN HASIL QUERY 'raport_categories' KAMU
            if ($jenis === 'unknown') {
                if ($kategoriId == 1) { 
                    $jenis = 'ganjil'; // Asumsi ID 1 itu Ganjil
                } elseif ($kategoriId == 2) {
                    $jenis = 'genap';  // Asumsi ID 2 itu Genap
                }
            }

            // Jika masih unknown, skip loop ini
            if ($jenis === 'unknown') continue;

            // Generate URL
            $parts = explode('/', $file->file_path);
            
            // Fix jika path mengandung folder (raport_files/nama.pdf)
            // Logic ini menangani path baik yang "folder/file.pdf" maupun "file.pdf" saja
            $filename = end($parts); // Ambil bagian terakhir (nama filenya)
            $folder   = count($parts) > 1 ? $parts[0] : 'raport_files'; // Default folder jika tidak terdeteksi
            
            // Namun karena kamu pakai route utility /storage/{folder}/{filename}
            // Kita harus pastikan formatnya benar
            if(count($parts) >= 2) {
                $url = url("/storage/{$parts[0]}/{$parts[1]}");
                $fileMap[$file->kelas_id][$jenis] = $url;
            }
        }

        return view('siswa.raport.index', compact('riwayatKelas', 'siswa', 'listTahun', 'fileMap'));  }  
    // Method show() bisa kita hapus atau biarkan saja (tidak terpakai di logic modal ini)
}