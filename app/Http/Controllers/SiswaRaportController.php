<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\RaportFile; 
use App\Models\RaportCategory; 

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
        // Menggunakan withPivot('tahun_ajaran_id') dari model Siswa
        $riwayatKelas = $siswa->riwayatKelas()
                        ->with(['waliKelas', 'tahunAjaran']) // Load relasi biar namanya muncul
                        ->get()
                        ->sortByDesc(function($kelas) {
                            return $kelas->pivot->tahun_ajaran_id; 
                        });

        // 2. Ambil List Tahun Ajaran (Kamus ID => Nama)
        $listTahun = TahunAjaran::pluck('tahun', 'id');

        // 3. LOGIC BARU: Mapping File Raport
        $files = RaportFile::with('category')
                    ->where('student_id', $siswa->id)
                    ->get();

        $fileMap = [];
        
        foreach ($files as $file) {
            // Logic Deteksi Ganjil/Genap
            $kategoriNama = strtolower($file->category->nama ?? '');
            $kategoriId   = $file->raport_category_id;
            
            $jenis = 'unknown';

            // Cek by Nama
            if (str_contains($kategoriNama, 'ganjil')) {
                $jenis = 'ganjil';
            } elseif (str_contains($kategoriNama, 'genap')) {
                $jenis = 'genap';
            }

            // Cek by ID (Fallback)
            if ($jenis === 'unknown') {
                if ($kategoriId == 1) $jenis = 'ganjil';
                elseif ($kategoriId == 2) $jenis = 'genap';
            }

            if ($jenis === 'unknown') continue;

            // Generate URL
            // Pastikan path-nya benar sesuai penyimpanan controller upload
            $url = asset('storage/' . $file->file_path);
            
            // --- PERBAIKAN UTAMA DISINI ---
            // Simpan berdasarkan [KELAS] DAN [TAHUN]
            // Agar raport tahun lalu tetap aman pada tempatnya
            $fileMap[$file->kelas_id][$file->tahun_ajaran_id][$jenis] = $url;
        }

        return view('siswa.raport.index', compact('riwayatKelas', 'siswa', 'listTahun', 'fileMap'));
    }
}