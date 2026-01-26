<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\TahunAjaran; 
use Illuminate\Http\Request;
use App\Imports\SiswaKelasImport;
use Maatwebsite\Excel\Facades\Excel;

class KelasController extends Controller
{
    private function getActiveTahunAjaran()
    {
        $ta = TahunAjaran::where('is_active', 1)->first(); 
        if(!$ta) {
            abort(404, 'Tahun Ajaran Aktif tidak ditemukan.');
        }
        return $ta;
    }

public function index()
{
    // 1. Ambil Tahun Ajaran Aktif
    $activeTa = $this->getActiveTahunAjaran(); 

    // 2. Ambil Kelas (FILTER: Hanya kelas milik tahun ini)
    $kelas = Kelas::where('tahun_ajaran_id', $activeTa->id) // <--- TAMBAHKAN BARIS INI
                  ->with('waliKelas')
                  ->withCount(['siswas' => function($query) use ($activeTa) {
                      $query->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
                  }])
                  ->orderBy('nama_kelas')
                  ->get();
    
    $gurus = Guru::orderBy('nama_lengkap')->get();
    
    return view('admin.kelas.index', compact('kelas', 'gurus'));
}
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required', // Hapus unique global, nanti kita bahas di bawah
            'wali_kelas_id' => 'required|exists:gurus,id',
        ]);

        // 1. Ambil Tahun Ajaran Aktif (Pasti Ada karena sudah dicek di fungsi getActiveTahunAjaran)
        $activeTa = $this->getActiveTahunAjaran();

        // 2. Cek Unik: Nama Kelas tidak boleh sama DALAM TAHUN YANG SAMA
        // (Kelas 1A boleh ada di 2024 dan 2025, tapi di 2024 tidak boleh ada dua Kelas 1A)
        $cekDuplikat = Kelas::where('nama_kelas', $request->nama_kelas)
                            ->where('tahun_ajaran_id', $activeTa->id)
                            ->exists();

        if ($cekDuplikat) {
            return back()->withErrors(['nama_kelas' => 'Nama kelas ini sudah ada di Tahun Ajaran aktif!']);
        }

        // 3. Simpan Kelas Baru
        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas_id' => $request->wali_kelas_id,
            'tahun_ajaran_id' => $activeTa->id, // <--- INI FIX-NYA (Ambil ID Tahun Aktif)
        ]);

        return redirect()->back()->with('success', 'Kelas berhasil dibuat untuk Tahun Ajaran ' . $activeTa->tahun);
    }
public function show($id)
{
    $activeTa = $this->getActiveTahunAjaran();
    $kelas = Kelas::findOrFail($id);

    // --- BAGIAN INI YANG SALAH SEBELUMNYA ---
    // Jangan pakai where() biasa, tapi wherePivot()
$siswas = $kelas->siswas()
                    ->wherePivot('tahun_ajaran_id', $activeTa->id) // <--- INI KUNCINYA
                    ->orderBy('nama_lengkap')
                    ->get();
    // Ambil siswa yang BELUM punya kelas di tahun ini (untuk dropdown tambah)
    // Logic: Cari siswa yang TIDAK ADA di tabel kelas_siswa pada tahun aktif ini
    $siswaNonKelas = Siswa::whereDoesntHave('riwayatKelas', function($q) use ($activeTa) {
        $q->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
    })->orderBy('nama_lengkap')->get();

    return view('admin.kelas.show', compact('kelas', 'siswas', 'siswaNonKelas', 'activeTa'));
}
    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        
        $request->validate([
            'nama_kelas' => 'required',
            'wali_kelas_id' => 'required'
        ]);
        
        // Cek Duplikat saat Update (Kecuali dirinya sendiri)
        $activeTaId = $kelas->tahun_ajaran_id; // Pakai tahun aslinya dia
        
        $cekDuplikat = Kelas::where('nama_kelas', $request->nama_kelas)
                            ->where('tahun_ajaran_id', $activeTaId)
                            ->where('id', '!=', $id) // Kecuali ID ini
                            ->exists();

        if ($cekDuplikat) {
            return back()->withErrors(['nama_kelas' => 'Nama kelas sudah digunakan di tahun ajaran ini.']);
        }

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas_id' => $request->wali_kelas_id
            // Tahun ajaran tidak perlu diupdate, biarkan sesuai saat dibuat
        ]);
        
        return redirect()->back()->with('success', 'Data kelas diperbarui');
    }
        public function destroy($id)

    {

        Kelas::findOrFail($id)->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas dihapus.');

    }




    // --- FITUR MANAJEMEN ANGGOTA KELAS (FIXED) ---

    // 1. Tambah Siswa (One-to-Many Logic)
public function addSiswa(Request $request, $id)
{
    $activeTa = $this->getActiveTahunAjaran();
    $kelas = Kelas::findOrFail($id);
    
    // Validasi input
    $request->validate([
        'siswa_id' => 'required|exists:siswas,id'
    ]);

    $siswaId = $request->siswa_id;

    // 1. CEK DUPLIKASI (Anti Error SQL)
    // Cek apakah siswa ini SUDAH ADA di kelas ini PADA TAHUN INI?
    $sudahAda = $kelas->siswas()
                      ->where('siswa_id', $siswaId) // Cek Siswanya
                      ->wherePivot('tahun_ajaran_id', $activeTa->id) // Cek Tahunnya
                      ->exists();

    if ($sudahAda) {
        return redirect()->back()->with('error', 'Siswa tersebut sudah ada di kelas ini pada Tahun Ajaran aktif!');
    }

    // 2. JIKA BELUM ADA, BARU SIMPAN (Attach)
    $kelas->siswas()->attach($siswaId, ['tahun_ajaran_id' => $activeTa->id]);

    return redirect()->back()->with('success', 'Siswa berhasil ditambahkan.');
}    // 2. Hapus Siswa (One-to-Many Logic)
public function removeSiswa($id_kelas, $id_siswa)
{
    $activeTa = $this->getActiveTahunAjaran();
    $kelas = Kelas::findOrFail($id_kelas);

    // Hapus dari tabel pivot (Detach) khusus tahun ini saja
    $kelas->siswas()->wherePivot('tahun_ajaran_id', $activeTa->id)->detach($id_siswa);

    return redirect()->back()->with('success', 'Siswa dikeluarkan.');
}
    public function importSiswa(Request $request, $id)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        $activeTa = $this->getActiveTahunAjaran();

        Excel::import(new SiswaKelasImport($id, $activeTa->id), $request->file('file'));
        
        return redirect()->back()->with('success', 'Import selesai!');
    }
}