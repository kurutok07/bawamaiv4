<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Imports\GuruImport;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('niy', 'LIKE', "%{$search}%")   // Ganti NUPTK jadi NIY
                  ->orWhere('nuptk', 'LIKE', "%{$search}%"); // Ganti NIP jadi NUPTK
            });
        }

        $gurus = $query->orderBy('nama_lengkap')->paginate(10);
        return view('admin.guru.index', compact('gurus'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (NIY Wajib, NUPTK Opsional)
        $request->validate([
            'niy'             => 'required|numeric|unique:gurus,niy|unique:users,username', // NIY jadi username
            'nuptk'           => 'nullable|numeric', // NUPTK sekarang opsional (bekas NIP)
            'nama_lengkap'    => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:L,P',
            'tempat_lahir'    => 'required|string',
            'tanggal_lahir'   => 'required|date',
            'status_kepegawaian' => 'required',
            'pendidikan_terakhir'=> 'required',
            'email'           => 'nullable|email|unique:users,email',
            'foto'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // 2. Upload Foto Logic
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('fotos', 'public');
            }

            // 3. Buat User Login (Username pakai NIY)
            // Format email dummy juga pakai NIY
            $email = $request->email ?? $request->niy . '@guru.bawamai.sch.id';
            
            $user = User::create([
                'name'     => $request->nama_lengkap,
                'username' => $request->niy, // Username pakai NIY
                'email'    => $email,
                'password' => Hash::make($request->niy), // Default password = NIY
                'role'     => 'guru',
            ]);

            // 4. Simpan Data Guru
            Guru::create([
                'user_id'            => $user->id,
                'niy'                => $request->niy,   // Simpan ke kolom niy
                'nuptk'              => $request->nuptk, // Simpan ke kolom nuptk (opsional)
                'nip'                => null, // Kolom NIP (jika masih ada di DB tapi tidak dipakai, set null)
                'nama_lengkap'       => $request->nama_lengkap,
                'gelar_depan'        => $request->gelar_depan,
                'gelar_belakang'     => $request->gelar_belakang,
                'jenis_kelamin'      => $request->jenis_kelamin,
                'tempat_lahir'       => $request->tempat_lahir,
                'tanggal_lahir'      => $request->tanggal_lahir,
                'no_hp'              => $request->no_hp,
                'email'              => $request->email,
                'alamat'             => $request->alamat,
                'foto'               => $fotoPath,
                
                // Field Kepegawaian & Pendidikan
                'status_kepegawaian' => $request->status_kepegawaian,
                'tugas_tambahan'     => $request->tugas_tambahan,
                'pendidikan_terakhir'=> $request->pendidikan_terakhir,
                'tahun_lulus'        => $request->tahun_lulus,
                'tmt_sekolah'        => $request->tmt_sekolah,
                'masa_kerja_sd'      => $request->masa_kerja_sd,
                'masa_kerja_total'   => $request->masa_kerja_total,
            ]);

            DB::commit();
            return redirect()->route('guru.index')->with('success', 'Data Guru & Akun berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollback();
            if ($fotoPath) Storage::disk('public')->delete($fotoPath);
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        // Ambil data guru beserta user login-nya
        $guru = Guru::with('user')->findOrFail($id);
        
        return view('admin.guru.show', compact('guru'));
    }
    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }
    // ... method import dll ...

    public function destroyAll()
    {
        // Ambil semua data guru
        $gurus = Guru::all();

        if ($gurus->isEmpty()) {
            return redirect()->back()->with('error', 'Data guru sudah kosong.');
        }

        foreach ($gurus as $guru) {
            // 1. Hapus Foto dari Storage (Jika ada)
            if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
                Storage::disk('public')->delete($guru->foto);
            }

            // 2. Hapus Akun Login (User)
            if ($guru->user) {
                $guru->user->delete();
            }

            // 3. Hapus Data Guru itu sendiri
            $guru->delete();
        }

        return redirect()->back()->with('success', 'SEMUA data guru dan akun login berhasil dihapus bersih!');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        // 1. Validasi Update
        $request->validate([
            // Cek unique NIY kecuali punya diri sendiri
            'niy'             => 'required|numeric|unique:gurus,niy,'.$id,
            'nuptk'           => 'nullable|numeric',
            'nama_lengkap'    => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:L,P',
            'tempat_lahir'    => 'required|string',
            'tanggal_lahir'   => 'required|date',
            'status_kepegawaian' => 'required',
            'pendidikan_terakhir'=> 'required',
            'email'           => 'nullable|email|unique:gurus,email,'.$id,
            'foto'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // 2. Cek apakah NIY berubah? (Untuk update User Login)
            $oldNiy = $guru->niy;
            $newNiy = $request->niy;

            // Jika NIY berubah, pastikan username di tabel Users belum dipakai orang lain
            if ($oldNiy != $newNiy) {
                $existUser = User::where('username', $newNiy)->where('id', '!=', $guru->user_id)->first();
                if ($existUser) {
                    return redirect()->back()->with('error', 'NIY sudah digunakan sebagai Username oleh akun lain!')->withInput();
                }
            }

            // 3. Logic Foto
            $fotoPath = $guru->foto;
            if ($request->hasFile('foto')) {
                if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
                    Storage::disk('public')->delete($guru->foto);
                }
                $fotoPath = $request->file('foto')->store('fotos', 'public');
            }

            // 4. Update Data Guru
            $guru->update([
                'niy'                => $request->niy,
                'nuptk'              => $request->nuptk,
                // 'nip' tidak perlu diupdate atau set null jika mau
                'nama_lengkap'       => $request->nama_lengkap,
                'gelar_depan'        => $request->gelar_depan,
                'gelar_belakang'     => $request->gelar_belakang,
                'jenis_kelamin'      => $request->jenis_kelamin,
                'tempat_lahir'       => $request->tempat_lahir,
                'tanggal_lahir'      => $request->tanggal_lahir,
                'no_hp'              => $request->no_hp,
                'email'              => $request->email,
                'alamat'             => $request->alamat,
                'foto'               => $fotoPath,
                
                'status_kepegawaian' => $request->status_kepegawaian,
                'tugas_tambahan'     => $request->tugas_tambahan,
                'pendidikan_terakhir'=> $request->pendidikan_terakhir,
                'tahun_lulus'        => $request->tahun_lulus,
                'tmt_sekolah'        => $request->tmt_sekolah,
                'masa_kerja_sd'      => $request->masa_kerja_sd,
                'masa_kerja_total'   => $request->masa_kerja_total,
            ]);

            // 5. Update User Login
            if ($guru->user) {
                $userData = [
                    'name'  => $request->nama_lengkap,
                    'email' => $request->email ?? $guru->user->email,
                ];

                // Jika NIY berubah, update username & reset password
                if ($oldNiy != $newNiy) {
                    $userData['username'] = $newNiy;
                    $userData['password'] = Hash::make($newNiy);
                }

                $guru->user->update($userData);
            }

            DB::commit();
            return redirect()->route('guru.index')->with('success', 'Data Guru berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->user) {
            $guru->user->delete();
        }

        if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
            Storage::disk('public')->delete($guru->foto);
        }

        $guru->delete();
        return redirect()->back()->with('success', 'Data Guru & Akun dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Guru berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}