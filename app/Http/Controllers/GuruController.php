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
    public function index()
    {
        $gurus = Guru::orderBy('nama_lengkap', 'asc')->get();
        return view('admin.guru.index', compact('gurus'));
    }

public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:gurus,nip|unique:users,username',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'foto' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $dataGuru = $request->all();

            // 1. Logic Foto
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('fotos', 'public');
                $dataGuru['foto'] = $path; 
            }

            // 2. SIMPAN USER DULUAN (Supaya dapat ID-nya)
            // Kita tampung hasilnya ke variabel $newUser
            $newUser = User::create([
                'name'      => $request->nama_lengkap,
                'username'  => $request->nip,
                'email'     => $request->email ?? $request->nip.'@bawamai.sch.id',
                'password'  => Hash::make($request->nip),
                'role'      => 'guru',
            ]);

            // 3. Masukkan user_id ke array data guru
            $dataGuru['user_id'] = $newUser->id; // <--- INI KUNCINYA

            // 4. Baru Simpan Guru
            Guru::create($dataGuru);

            DB::commit();
            return redirect()->back()->with('success', 'Data Guru & Akun berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Hapus foto jika gagal database
            if (isset($dataGuru['foto'])) {
                Storage::disk('public')->delete($dataGuru['foto']);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nip'            => 'required|max:20|unique:gurus,nip,'.$id,
            'nama_lengkap'   => 'required|string|max:255',
            'gelar_depan'    => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'no_hp'          => 'nullable|string|max:15',
            'email'          => 'nullable|email|unique:gurus,email,'.$id,
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $guru = Guru::findOrFail($id);
        $oldNip = $guru->nip;

        $data = [
            'nip'            => $request->nip,
            'nama_lengkap'   => $request->nama_lengkap,
            'gelar_depan'    => $request->gelar_depan,
            'gelar_belakang' => $request->gelar_belakang,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'no_hp'          => $request->no_hp,
            'email'          => $request->email,
        ];

        // Logic Update Foto (Sesuai Route Manual)
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
                Storage::disk('public')->delete($guru->foto);
            }
            // Upload baru
            $path = $request->file('foto')->store('fotos', 'public');
            $data['foto'] = $path;
        }

        $guru->update($data);

        // Update User juga
        $user = User::where('username', $oldNip)->first();
        if($user) {
            $user->update([
                'name' => $request->nama_lengkap,
                'username' => $request->nip, // Update username jika NIP berubah
                'email' => $request->email ?? $user->email
            ]);
        }

        return redirect()->route('guru.index')->with('success', 'Data Guru berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        
        // Hapus User Login
        if ($guru->user) {
            $guru->user->delete();
        }

        // Hapus Foto Fisik
        if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
            Storage::disk('public')->delete($guru->foto);
        }
        
        $guru->delete();
        return redirect()->back()->with('success', 'Data Guru & Akun dihapus');
    }

    // --- INI METHOD YANG HILANG TADI ---
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