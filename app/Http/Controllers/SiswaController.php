<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User; // <--- PENTING: Jangan lupa import User
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; // <--- PENTING: Jangan lupa import Hash
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    /**
     * 1. INDEX: Menampilkan halaman daftar siswa
     */
    public function index()
    {
        // Mengambil data urut dari yang paling baru dibuat
        $siswas = Siswa::latest()->get();
        return view('admin.siswa.index', compact('siswas'));
    }

    /**
     * 2. STORE: Menyimpan data siswa baru
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'nis' => 'required|unique:siswas,nis|unique:users,username', // Pastikan NIS unik di kedua tabel
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle Upload Foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_siswa', 'public');
        }

        // Simpan ke Database dengan Transaction (Agar kalau satu gagal, semua batal)
        DB::transaction(function () use ($request, $fotoPath) {
        
            // A. Buat Akun User (Untuk Login)
            $user = User::create([
                'name' => $request->nama_lengkap,
                // Email dummy pattern: nis@siswa.bawamai.id
                'email' => $request->nis . '@siswa.bawamai.id', 
                'username' => $request->nis, // Username pakai NIS
                'password' => Hash::make($request->nis), // Password default = NIS
                'role' => 'siswa',
            ]);

            // B. Buat Data Siswa (Link ke User)
            Siswa::create([
                'user_id' => $user->id, // <--- PENGAIT KUNCI (RELASI)
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'nama_wali' => $request->nama_wali,
                'no_hp_wali' => $request->no_hp_wali,
                'alamat' => $request->alamat,
                // 'tahun_ajaran_id' => $request->tahun_ajaran_id, // Opsional, saran saya biarkan NULL karena pakai pivot kelas
                'foto' => $fotoPath,
            ]);
        });

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    /**
     * 3. EDIT: Menampilkan form edit
     */
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('admin.siswa.edit', compact('siswa'));
    }

    /**
     * 4. UPDATE: Memperbarui data siswa
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        // Validasi (Ignore unique ID milik sendiri)
        $request->validate([
            'nis'           => 'required|max:20|unique:siswas,nis,' . $siswa->id,
            'nama_lengkap'  => 'required',
            'jenis_kelamin' => 'required',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Data yang akan diupdate di tabel Siswa
        $data = [
            'nis'           => $request->nis,
            'nisn'          => $request->nisn,
            'nama_lengkap'  => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nama_wali'     => $request->nama_wali,
            'no_hp_wali'    => $request->no_hp_wali,
            'alamat'        => $request->alamat,
        ];

        // Cek apakah ada upload foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }
            // Simpan foto baru
            $data['foto'] = $request->file('foto')->store('foto_siswa', 'public');
        }

        // Update Tabel Siswa
        $siswa->update($data);

        // --- SINKRONISASI DATA LOGIN (USER) ---
        // Jika Nama atau NIS berubah, update juga di tabel Users agar login tetap jalan
        if ($siswa->user_id) {
            $user = User::find($siswa->user_id);
            if ($user) {
                $user->update([
                    'name' => $request->nama_lengkap,
                    'username' => $request->nis, // NIS berubah -> Username berubah
                    'email' => $request->nis . '@siswa.bawamai.id' // Update email pattern
                ]);
            }
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * 5. DESTROY: Menghapus data siswa
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // 1. Hapus File Foto Fisik (Penting agar storage tidak penuh)
        if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        // 2. Hapus Akun Login (User)
        if ($siswa->user_id) {
            User::where('id', $siswa->user_id)->delete();
        }
        
        // 3. Hapus Data Siswa
        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa dan akun login berhasil dihapus.');
    }

    /**
     * 6. IMPORT: Fitur Import Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            // Import menggunakan logic baru yang sudah kita perbaiki tadi (Bikin User -> Bikin Siswa)
            Excel::import(new SiswaImport, $request->file('file'));
            
            return redirect()->back()->with('success', 'Import Data Siswa Berhasil!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }
}