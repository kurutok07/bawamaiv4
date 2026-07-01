<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\TahunAjaran; 
use Illuminate\Http\Request;
use App\Imports\SiswaKelasImport; 
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File; // <--- PENTING: Untuk hapus file lama

class KelasController extends Controller
{
    // --- HELPER: Ambil Tahun Ajaran Aktif ---
    private function getActiveTahunAjaran()
    {
        $ta = TahunAjaran::where('is_active', 1)->first(); 
        if(!$ta) {
            abort(404, 'Tahun Ajaran Aktif tidak diset. Silakan hubungi Admin untuk mengaktifkan Tahun Ajaran.');
        }
        return $ta;
    }

    public function index()
    {
        $activeTa = $this->getActiveTahunAjaran(); 

        $kelas = Kelas::where('tahun_ajaran_id', $activeTa->id)
                      ->with('waliKelas') 
                      ->withCount(['siswas' => function($query) use ($activeTa) {
                          $query->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
                      }])
                      ->orderBy('tingkat', 'asc')
                      ->orderBy('nama_kelas', 'asc')
                      ->get();
        
        $gurus = Guru::orderBy('nama_lengkap')->get();
        
        return view('admin.kelas.index', compact('kelas', 'gurus', 'activeTa'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama_kelas'    => 'required', 
            'tingkat'       => 'required|integer',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            'file_jadwal'   => 'nullable|mimes:pdf|max:2048', // <--- Validasi PDF Max 2MB
        ]);

        $activeTa = $this->getActiveTahunAjaran();

        // 2. Cek Duplikat
        $cekDuplikat = Kelas::where('nama_kelas', $request->nama_kelas)
                            ->where('tahun_ajaran_id', $activeTa->id)
                            ->exists();

        if ($cekDuplikat) {
            return back()->withErrors(['nama_kelas' => 'Nama kelas ini sudah ada di Tahun Ajaran aktif!']);
        }

        // 3. Siapkan Data Input
        $input = [
            'nama_kelas'      => $request->nama_kelas,
            'tingkat'         => $request->tingkat,
            'wali_kelas_id'   => $request->wali_kelas_id,
            'tahun_ajaran_id' => $activeTa->id,
            'file_jadwal'     => null // Default null
        ];

        // 4. Logic Upload File Jadwal
        if ($request->hasFile('file_jadwal')) {
            $file = $request->file('file_jadwal');
            $filename = 'jadwal_' . time() . '_' . $file->getClientOriginalName();
            
            // Simpan ke folder public/uploads/jadwal
            $file->move(public_path('uploads/jadwal'), $filename);
            
            // Simpan path ke database
            $input['file_jadwal'] = 'uploads/jadwal/' . $filename;
        }

        Kelas::create($input);

        return redirect()->back()->with('success', 'Kelas berhasil dibuat untuk T.A. ' . $activeTa->tahun_ajaran);
    }

    public function show($id)
    {
        $activeTa = $this->getActiveTahunAjaran();
        $kelas = Kelas::findOrFail($id);

        $siswas = $kelas->siswas()
                        ->wherePivot('tahun_ajaran_id', $activeTa->id)
                        ->orderBy('nama_lengkap')
                        ->get();
        
        $gurusPengajar = $kelas->gurus()
                            ->wherePivot('tahun_ajaran_id', $activeTa->id)
                            ->orderBy('nama_lengkap')
                            ->get();

        $siswaNonKelas = Siswa::whereDoesntHave('riwayatKelas', function($q) use ($activeTa) {
            $q->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
        })->orderBy('nama_lengkap')->get();

        $guruNonPengajar = Guru::whereDoesntHave('kelasAjar', function($q) use ($kelas, $activeTa) {
            $q->where('guru_kelas.kelas_id', $kelas->id)
            ->where('guru_kelas.tahun_ajaran_id', $activeTa->id);
        })->orderBy('nama_lengkap')->get();

        return view('admin.kelas.show', compact('kelas', 'siswas', 'gurusPengajar', 'siswaNonKelas', 'guruNonPengajar', 'activeTa'));
    }

    public function addGuru(Request $request, $id)
    {
        $activeTa = $this->getActiveTahunAjaran();
        $kelas = Kelas::findOrFail($id);
        
        $request->validate([
            'guru_id' => 'required|exists:gurus,id'
        ]);

        $exists = $kelas->gurus()
                        ->where('guru_id', $request->guru_id)
                        ->wherePivot('tahun_ajaran_id', $activeTa->id)
                        ->exists();

        if (!$exists) {
            $kelas->gurus()->attach($request->guru_id, ['tahun_ajaran_id' => $activeTa->id]);
            return redirect()->back()->with('success', 'Guru berhasil ditambahkan sebagai pengajar.');
        }

        return redirect()->back()->with('error', 'Guru tersebut sudah mengajar di kelas ini.');
    }

    public function removeGuru($id_kelas, $id_guru)
    {
        $activeTa = $this->getActiveTahunAjaran();
        $kelas = Kelas::findOrFail($id_kelas);
        $kelas->gurus()->wherePivot('tahun_ajaran_id', $activeTa->id)->detach($id_guru);

        return redirect()->back()->with('success', 'Guru dihapus dari daftar pengajar.');
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        
        // 1. Validasi
        $request->validate([
            'nama_kelas'    => 'required',
            'tingkat'       => 'required|integer',
            'wali_kelas_id' => 'nullable',
            'file_jadwal'   => 'nullable|mimes:pdf|max:2048', // <--- Validasi PDF
        ]);
        
        $activeTaId = $kelas->tahun_ajaran_id; 
        
        // 2. Cek Duplikat Nama
        $cekDuplikat = Kelas::where('nama_kelas', $request->nama_kelas)
                            ->where('tahun_ajaran_id', $activeTaId)
                            ->where('id', '!=', $id)
                            ->exists();

        if ($cekDuplikat) {
            return back()->withErrors(['nama_kelas' => 'Nama kelas sudah digunakan di tahun ajaran ini.']);
        }

        // 3. Siapkan Array Update
        $input = [
            'nama_kelas'    => $request->nama_kelas,
            'tingkat'       => $request->tingkat,
            'wali_kelas_id' => $request->wali_kelas_id
        ];

        // 4. Logic Update File Jadwal
        if ($request->hasFile('file_jadwal')) {
            // A. Hapus file lama jika ada
            if ($kelas->file_jadwal && File::exists(public_path($kelas->file_jadwal))) {
                File::delete(public_path($kelas->file_jadwal));
            }

            // B. Upload file baru
            $file = $request->file('file_jadwal');
            $filename = 'jadwal_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/jadwal'), $filename);
            
            // C. Masukkan path baru ke array update
            $input['file_jadwal'] = 'uploads/jadwal/' . $filename;
        }

        $kelas->update($input);
        
        return redirect()->back()->with('success', 'Data kelas & jadwal berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        // Hapus file jadwal jika ada sebelum menghapus data kelas
        if ($kelas->file_jadwal && File::exists(public_path($kelas->file_jadwal))) {
            File::delete(public_path($kelas->file_jadwal));
        }

        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas dihapus.');
    }

    // --- FITUR MANAJEMEN ANGGOTA KELAS ---

public function addSiswa(Request $request, $id_kelas)
    {
        $activeTa = $this->getActiveTahunAjaran();
        $kelasTujuan = Kelas::findOrFail($id_kelas);
        
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id'
        ]);

        $siswaId = $request->siswa_id;

        // 1. Cek apakah siswa SUDAH punya kelas di Tahun Ajaran Aktif?
        // Kita cari di tabel pivot 'kelas_siswa'
        $existingRecord = \Illuminate\Support\Facades\DB::table('kelas_siswa')
                            ->where('siswa_id', $siswaId)
                            ->where('tahun_ajaran_id', $activeTa->id)
                            ->first();

        if ($existingRecord) {
            // Skenario A: Siswa SUDAH punya kelas di tahun ini (Pindah Kelas / Koreksi)
            
            // Cek apakah kelasnya sama?
            if ($existingRecord->kelas_id == $id_kelas) {
                return redirect()->back()->with('error', 'Siswa sudah ada di kelas ini.');
            }

            // Jika beda kelas, kita UPDATE record yang ada (bukan insert baru)
            // Agar siswa tidak punya 2 kelas di tahun yang sama.
            \Illuminate\Support\Facades\DB::table('kelas_siswa')
                ->where('id', $existingRecord->id)
                ->update([
                    'kelas_id' => $id_kelas,
                    'updated_at' => now()
                ]);

            return redirect()->back()->with('success', 'Siswa berhasil dipindahkan ke kelas ini.');

        } else {
            // Skenario B: Siswa BELUM punya kelas di tahun ini (Naik Kelas / Siswa Baru)
            // Kita INSERT record baru. Record tahun lalu aman (tidak tersentuh).
            
            $kelasTujuan->siswas()->attach($siswaId, [
                'tahun_ajaran_id' => $activeTa->id
            ]);

            return redirect()->back()->with('success', 'Siswa berhasil ditambahkan.');
        }
    }
    
    public function removeSiswa($id_kelas, $id_siswa)
    {
        $activeTa = $this->getActiveTahunAjaran();
        $kelas = Kelas::findOrFail($id_kelas);

        $kelas->siswas()->wherePivot('tahun_ajaran_id', $activeTa->id)->detach($id_siswa);

        return redirect()->back()->with('success', 'Siswa dikeluarkan dari kelas.');
    }

    public function importSiswa(Request $request, $id)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        
        $activeTa = $this->getActiveTahunAjaran();

        try {
            Excel::import(new SiswaKelasImport($id, $activeTa->id), $request->file('file'));
            return redirect()->back()->with('success', 'Import siswa berhasil!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
