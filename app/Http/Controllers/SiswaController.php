<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User; 
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\SiswaFamily; // Import Model Family
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    private function getActiveTahunAjaran()
    {
        return TahunAjaran::where('is_active', 1)->first(); 
    }

public function index(Request $request)
    {
        $activeTa = $this->getActiveTahunAjaran();
        
        $query = Siswa::with(['kelas' => function($q) use ($activeTa) {
            if($activeTa) $q->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
        }]);

        // 1. Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // 2. Filter Kelas
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            if ($request->kelas_id == 'tanpa_kelas') {
                $query->whereDoesntHave('kelas', function($q) use ($activeTa) {
                    if($activeTa) $q->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
                });
            } else {
                $query->whereHas('kelas', function($q) use ($request, $activeTa) {
                    $q->where('kelas_id', $request->kelas_id);
                    if($activeTa) $q->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
                });
            }
        }

        // --- TAMBAHKAN KODE INI UNTUK FILTER GENDER ---
        // 3. Filter Gender (Jenis Kelamin)
        if ($request->has('jk') && $request->jk != '') {
            $query->where('jenis_kelamin', $request->jk);
        }
        // ----------------------------------------------

        $siswas = $query->latest()->paginate(10)->withQueryString(); 
        $daftarKelas = $activeTa ? Kelas::where('tahun_ajaran_id', $activeTa->id)->orderBy('nama_kelas')->get() : [];

        return view('admin.siswa.index', compact('siswas', 'daftarKelas', 'activeTa'));
    }
        
    public function create()
    {
        $activeTa = $this->getActiveTahunAjaran();
        $daftarKelas = $activeTa ? Kelas::where('tahun_ajaran_id', $activeTa->id)->get() : [];
        return view('admin.siswa.create', compact('daftarKelas'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            // Identitas Utama
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'nisn'          => 'required|numeric|unique:siswas,nisn|unique:users,username',
            'nipd'          => 'nullable|string|max:50',
            'nik'           => 'nullable|numeric|unique:siswas,nik',
            'tempat_lahir'  => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            
            // Kelas & Foto
            'kelas_id'      => 'nullable|exists:kelas,id',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Data Tambahan (Validasi nullable agar aman jika input ada)
            'alamat'        => 'nullable|string',
            'lintang'       => 'nullable|string',
            'bujur'         => 'nullable|string',
            'no_kip'        => 'nullable|string',
            'no_kps'        => 'nullable|string',
            'hp'            => 'nullable|string|max:20',
        ]);

        $activeTa = $this->getActiveTahunAjaran();

        // 2. Upload Foto Logic
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'siswa_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/siswa'), $filename);
            $fotoPath = 'uploads/siswa/' . $filename;
        }

        DB::transaction(function () use ($request, $fotoPath, $activeTa) {
            
            // 3. Buat User Login (Tetap NISN)
            $user = User::create([
                'name'     => $request->nama_lengkap,
                'email'    => $request->nisn . '@siswa.bawamai.id',
                'username' => $request->nisn,
                'password' => Hash::make($request->nisn), // Password default: NISN
                'role'     => 'siswa',
                'avatar'   => $fotoPath, 
            ]);

            // 4. Buat Data Siswa
            // Kita ambil semua input, lalu buang token, password, dan field keluarga/kelas/foto
            $siswaData = $request->except([
                '_token', 'password', 'kelas_id', 'foto', 
                // Exclude data keluarga (karena disimpan di tabel terpisah)
                'ayah_nama', 'ayah_nik', 'ayah_pekerjaan', 'ayah_pendidikan', 'ayah_penghasilan', 'ayah_tahun_lahir',
                'ibu_nama', 'ibu_nik', 'ibu_pekerjaan', 'ibu_pendidikan', 'ibu_penghasilan', 'ibu_tahun_lahir',
                'wali_nama', 'wali_nik', 'wali_pekerjaan', 'wali_pendidikan', 'wali_penghasilan', 'wali_tahun_lahir'
            ]);
            
            // Inject foreign key user & path foto
            $siswaData['user_id'] = $user->id;
            $siswaData['foto'] = $fotoPath;

            // Proses penyimpanan ke tabel 'siswas'
            // Pastikan model Siswa $fillable-nya sudah mencakup kolom-kolom baru (lintang, bujur, dll)
            // atau gunakan $guarded = ['id'] di model Siswa agar aman.
            $siswa = Siswa::create($siswaData);

            // 5. Simpan Data Keluarga
            $this->storeFamilyData($siswa, $request);

            // 6. Masukkan ke Kelas (Jika dipilih)
            if ($request->kelas_id && $activeTa) {
                $siswa->kelas()->attach($request->kelas_id, ['tahun_ajaran_id' => $activeTa->id]);
            }
        });

        return redirect()->route('siswa.index')->with('success', 'Data siswa lengkap berhasil disimpan!');
    }
    public function edit($id)
    {
        // Eager load data keluarga biar form terisi
        $siswa = Siswa::with(['ayah', 'ibu', 'wali'])->findOrFail($id);
        
        $activeTa = $this->getActiveTahunAjaran();
        $daftarKelas = $activeTa ? Kelas::where('tahun_ajaran_id', $activeTa->id)->orderBy('nama_kelas')->get() : [];
        
        $currentKelasID = null;
        if($activeTa) {
            $currentClass = $siswa->kelas()->wherePivot('tahun_ajaran_id', $activeTa->id)->first();
            $currentKelasID = $currentClass ? $currentClass->id : null;
        }

        return view('admin.siswa.edit', compact('siswa', 'daftarKelas', 'currentKelasID'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $activeTa = $this->getActiveTahunAjaran();

        $request->validate([
            'nama_lengkap' => 'required',
            'nisn'         => 'required|unique:siswas,nisn,' . $siswa->id,
            'kelas_id'     => 'nullable|exists:kelas,id',
        ]);

        // 1. Handle Foto
        $fotoPath = $siswa->foto;
        if ($request->hasFile('foto')) {
            if ($siswa->foto && File::exists(public_path($siswa->foto))) {
                File::delete(public_path($siswa->foto));
            }
            $file = $request->file('foto');
            $filename = 'siswa_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/siswa'), $filename);
            $fotoPath = 'uploads/siswa/' . $filename;
        }

        // 2. Update Siswa (Tabel Utama)
        $siswaData = $request->except(['_token', '_method', 'password', 'kelas_id', 'foto', 
             // Exclude data keluarga agar tidak error column not found
            'ayah_nama', 'ayah_nik', 'ayah_pekerjaan', 'ayah_pendidikan', 'ayah_penghasilan', 'ayah_tahun_lahir',
            'ibu_nama', 'ibu_nik', 'ibu_pekerjaan', 'ibu_pendidikan', 'ibu_penghasilan', 'ibu_tahun_lahir',
            'wali_nama', 'wali_nik', 'wali_pekerjaan', 'wali_pendidikan', 'wali_penghasilan', 'wali_tahun_lahir'
        ]);

        $siswaData['foto'] = $fotoPath;
        $siswa->update($siswaData);

        // 3. Update Data Keluarga (Pakai updateOrCreate)
        $this->updateFamilyData($siswa, $request);

        // 4. Update User Login
        if ($siswa->user_id) {
            $user = User::find($siswa->user_id);
            if($user) {
                $user->update([
                    'name'     => $request->nama_lengkap, 
                    'username' => $request->nisn,
                    'email'    => $request->nisn.'@siswa.bawamai.id',
                    'avatar'   => $fotoPath
                ]);
            }
        }

        // 5. Update Kelas Logic (Sama seperti sebelumnya)
        if ($activeTa) {
            $currentClassRecord = DB::table('kelas_siswa')
                                    ->where('siswa_id', $siswa->id)
                                    ->where('tahun_ajaran_id', $activeTa->id)
                                    ->first();
            $newKelasId = $request->kelas_id;

            if ($currentClassRecord) {
                if ($newKelasId && $newKelasId != $currentClassRecord->kelas_id) {
                    DB::table('kelas_siswa')->where('id', $currentClassRecord->id)
                        ->update(['kelas_id' => $newKelasId, 'updated_at' => now()]);
                } elseif (!$newKelasId) {
                    DB::table('kelas_siswa')->where('id', $currentClassRecord->id)->delete();
                }
            } else {
                if ($newKelasId) {
                    $siswa->kelas()->attach($newKelasId, ['tahun_ajaran_id' => $activeTa->id]);
                }
            }
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa lengkap diperbarui!');
    }
    public function destroyAll()
    {
        // 1. Ambil semua data siswa
        $siswas = Siswa::all();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Data siswa sudah kosong.');
        }

        // 2. Loop untuk hapus bersih
        foreach ($siswas as $siswa) {
            
            // A. Hapus Foto Fisik
            if ($siswa->foto && File::exists(public_path($siswa->foto))) {
                File::delete(public_path($siswa->foto));
            }

            // B. Hapus Akun Login
            if ($siswa->user_id) {
                User::where('id', $siswa->user_id)->delete();
            }

            // C. Hapus Data Siswa (Otomatis hapus Family & Health Log jika DB Cascade di-set)
            $siswa->delete();
        }

        return redirect()->back()->with('success', 'SEMUA data siswa, foto, dan akun login berhasil dihapus bersih!');
    }
    // --- HELPER UNTUK MENYIMPAN FAMILY (PRIVATE) ---
    private function storeFamilyData($siswa, $request)
    {
        // AYAH
        if($request->ayah_nama) {
            SiswaFamily::create([
                'siswa_id' => $siswa->id,
                'hubungan' => 'ayah',
                'nama' => $request->ayah_nama,
                'nik' => $request->ayah_nik,
                'tahun_lahir' => $request->ayah_tahun_lahir,
                'jenjang_pendidikan' => $request->ayah_pendidikan,
                'pekerjaan' => $request->ayah_pekerjaan,
                'penghasilan' => $request->ayah_penghasilan,
            ]);
        }
        // IBU
        if($request->ibu_nama) {
            SiswaFamily::create([
                'siswa_id' => $siswa->id,
                'hubungan' => 'ibu',
                'nama' => $request->ibu_nama,
                'nik' => $request->ibu_nik,
                'tahun_lahir' => $request->ibu_tahun_lahir,
                'jenjang_pendidikan' => $request->ibu_pendidikan,
                'pekerjaan' => $request->ibu_pekerjaan,
                'penghasilan' => $request->ibu_penghasilan,
            ]);
        }
        // WALI
        if($request->wali_nama) {
            SiswaFamily::create([
                'siswa_id' => $siswa->id,
                'hubungan' => 'wali',
                'nama' => $request->wali_nama,
                'nik' => $request->wali_nik,
                'tahun_lahir' => $request->wali_tahun_lahir,
                'jenjang_pendidikan' => $request->wali_pendidikan,
                'pekerjaan' => $request->wali_pekerjaan,
                'penghasilan' => $request->wali_penghasilan,
            ]);
        }
    }

    private function updateFamilyData($siswa, $request)
    {
        // Gunakan updateOrCreate: Jika ada update, jika tidak create.
        
        // AYAH
        $siswa->families()->updateOrCreate(
            ['hubungan' => 'ayah'],
            [
                'nama' => $request->ayah_nama,
                'nik' => $request->ayah_nik,
                'tahun_lahir' => $request->ayah_tahun_lahir,
                'jenjang_pendidikan' => $request->ayah_pendidikan,
                'pekerjaan' => $request->ayah_pekerjaan,
                'penghasilan' => $request->ayah_penghasilan,
            ]
        );

        // IBU
        $siswa->families()->updateOrCreate(
            ['hubungan' => 'ibu'],
            [
                'nama' => $request->ibu_nama,
                'nik' => $request->ibu_nik,
                'tahun_lahir' => $request->ibu_tahun_lahir,
                'jenjang_pendidikan' => $request->ibu_pendidikan,
                'pekerjaan' => $request->ibu_pekerjaan,
                'penghasilan' => $request->ibu_penghasilan,
            ]
        );

        // WALI (Hanya jika nama wali diisi, atau sudah ada datanya)
        if($request->filled('wali_nama') || $siswa->wali) {
            $siswa->families()->updateOrCreate(
                ['hubungan' => 'wali'],
                [
                    'nama' => $request->wali_nama,
                    'nik' => $request->wali_nik,
                    'tahun_lahir' => $request->wali_tahun_lahir,
                    'jenjang_pendidikan' => $request->wali_pendidikan,
                    'pekerjaan' => $request->wali_pekerjaan,
                    'penghasilan' => $request->wali_penghasilan,
                ]
            );
        }
    }
    public function show($id)
    {
        // Kita gunakan Eager Loading (with) agar query database efisien
        // Ambil data siswa + relasi families (ayah/ibu/wali) + riwayat kelas & tahun ajaran
        $siswa = Siswa::with(['families', 'kelas.tahunAjaran'])->findOrFail($id);

        return view('admin.siswa.show', compact('siswa'));
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        if ($siswa->foto && File::exists(public_path($siswa->foto))) {
            File::delete(public_path($siswa->foto));
        }
        if ($siswa->user_id) User::where('id', $siswa->user_id)->delete();
        $siswa->delete(); // Cascade delete akan menghapus family otomatis jika di migration diset cascade
        
        return redirect()->back()->with('success', 'Data siswa dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        try {
            // 1. Buat Instance Object Import terlebih dahulu
            // Kita butuh variabel ini untuk mengakses counter sukses/gagal setelah proses selesai
            $importer = new SiswaImport(); 

            // 2. Jalankan Import menggunakan object tersebut
            Excel::import($importer, $request->file('file'));

            // 3. Ambil Hasil Hitungan dari Object $importer
            $sukses = $importer->successCount;
            $gagal  = $importer->failCount;

            // 4. Susun Pesan Notifikasi
            $msg = "Proses Import Selesai! Berhasil: $sukses siswa.";
            
            // Jika ada yang gagal, tambahkan infonya dan gunakan alert 'warning'
            if ($gagal > 0) {
                $msg .= " Gagal/Dilewati: $gagal baris (Data kosong atau format salah).";
                return redirect()->back()->with('warning', $msg);
            }

            // Jika semua sukses
            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import Fatal: ' . $e->getMessage());
        }
    }
}