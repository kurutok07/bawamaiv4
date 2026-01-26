<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Kelas;
use App\Models\RaportCategory;
use App\Models\RaportFile;
use App\Models\TahunAjaran;
use App\Models\Guru; // <--- Jangan lupa import Model Guru
class RaportGuruController extends Controller
{
    // 1. HALAMAN PILIH KELAS
public function index()
{
    // 1. Ambil Tahun Ajaran Aktif
    $activeYear = \App\Models\TahunAjaran::where('is_active', 1)->first();
    if (!$activeYear) {
        return back()->with('error', 'Belum ada Tahun Ajaran yang aktif.');
    }

    // 2. Cari Guru berdasarkan User yang Login
    $guru = \App\Models\Guru::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();

    if (!$guru) {
        return back()->with('error', 'Akun Anda belum terhubung ke data Guru. Hubungi Admin.');
    }

    // 3. Ambil Kelas milik Guru tersebut
    // Note: Pastikan di tabel kelas, tahun_ajaran_id nya cocok dengan $activeYear->id
    $classes = \App\Models\Kelas::where('wali_kelas_id', $guru->id)
                        ->where('tahun_ajaran_id', $activeYear->id)
                        ->withCount('siswas') // <--- Tambahkan ini agar hitungan lebih cepat
                        ->get();
    return view('guru.raport.index', compact('classes', 'activeYear'));
}
    // 2. HALAMAN DETAIL (TABEL SISWA & UPLOAD)
public function show(Request $request, $kelas_id)
    {
        // Load relasi siswas (jamak) dan tahunAjaran
        $kelas = Kelas::with(['siswas', 'tahunAjaran'])->findOrFail($kelas_id);
        
        // --- PERBAIKAN LOGIKA AUTH ---
        // 1. Cari dulu Data Guru milik User yang sedang login
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();

        // 2. Jika bukan guru (atau belum dilink), tolak
        if (!$guru) {
            abort(403, 'Akun Anda tidak terhubung ke data Guru.');
        }

        // 3. Bandingkan ID Guru dengan Wali Kelas ID
        if ($kelas->wali_kelas_id != $guru->id) {
            abort(403, 'Anda bukan wali kelas dari kelas ini.');
        }
        // -----------------------------

        // --- LOGIC KATEGORI PINTAR ---
        // Ambil kategori dasar
        $categories = RaportCategory::whereIn('nama_kategori', ['Semester Ganjil', 'Semester Genap'])->get();

        // Jika Kelas 6, tambahkan Ijazah
        if (str_contains($kelas->nama_kelas, '6')) {
            $ijazah = RaportCategory::where('nama_kategori', 'Ijazah')->first();
            if($ijazah) $categories->push($ijazah);
        }

        // Tentukan kategori yang sedang dipilih
        $selectedCategoryId = $request->query('category_id', $categories->first()->id ?? 0);
        
        // Ambil File Raport
        $existingFiles = RaportFile::where('kelas_id', $kelas->id)
                            ->where('raport_category_id', $selectedCategoryId)
                            ->get()
                            ->keyBy('student_id');

        return view('guru.raport.show', compact('kelas', 'categories', 'selectedCategoryId', 'existingFiles'));
    }
    // 3. PROSES UPLOAD FILE
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'kelas_id' => 'required',
            'raport_category_id' => 'required',
            'file_raport' => 'required|mimes:pdf|max:2048', // Max 2MB
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);

        // Upload File
        $path = $request->file('file_raport')->store('raport_files', 'public');

        // Simpan ke Database
        // Pake updateOrCreate agar jika upload ulang, file lama terganti (recordnya)
        // Tapi file fisik lama sebaiknya dihapus manual dulu kalau mau hemat storage
        
        // Cek file lama untuk dihapus fisiknya
        $oldFile = RaportFile::where('student_id', $request->student_id)
                    ->where('kelas_id', $request->kelas_id)
                    ->where('raport_category_id', $request->raport_category_id)
                    ->first();

        if($oldFile) {
            Storage::disk('public')->delete($oldFile->file_path);
        }

        RaportFile::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'kelas_id' => $request->kelas_id,
                'raport_category_id' => $request->raport_category_id,
            ],
            [
                'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
                'uploaded_by' => Auth::id(),
                'file_path' => $path
            ]
        );

        return back()->with('success', 'File raport berhasil diupload.');
    }

    // 4. HAPUS FILE
    public function destroy($id)
    {
        $raport = RaportFile::findOrFail($id);
        
        // Cek hak akses (hanya pengupload yang boleh hapus)
        if($raport->uploaded_by != Auth::id()) {
            abort(403);
        }

        // Hapus fisik file
        Storage::disk('public')->delete($raport->file_path);
        
        // Hapus record
        $raport->delete();

        return back()->with('success', 'File raport berhasil dihapus.');
    }
}