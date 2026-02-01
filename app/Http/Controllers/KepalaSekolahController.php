<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File; // <--- PENTING: Import Facade File

class KepalaSekolahController extends Controller
{
    public function index()
    {
        $kepseks = User::where('role', 'kepala_sekolah')->latest()->get();
        return view('admin.kepala_sekolah.index', compact('kepseks'));
    }

    public function create()
    {
        return view('admin.kepala_sekolah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'foto'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi Foto
        ]);

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'kepala_sekolah',
        ];

        // --- LOGIC UPLOAD FOTO ---
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'kepsek_' . time() . '.' . $file->getClientOriginalExtension();
            $path = public_path('uploads/kepsek');
            
            // Buat folder jika belum ada
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $file->move($path, $filename);
            $data['foto_profil'] = 'uploads/kepsek/' . $filename; // Simpan path ke DB
        }

        User::create($data);

        return redirect()->route('admin.kepala-sekolah.index')
                         ->with('success', 'Akun Kepala Sekolah berhasil dibuat.');
    }

    public function edit($id)
    {
        $kepsek = User::where('role', 'kepala_sekolah')->findOrFail($id);
        return view('admin.kepala_sekolah.edit', compact('kepsek'));
    }

    public function update(Request $request, $id)
    {
        $kepsek = User::where('role', 'kepala_sekolah')->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'foto'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // --- LOGIC UPDATE FOTO ---
        if ($request->hasFile('foto')) {
            // 1. Hapus foto lama jika ada
            if ($kepsek->foto_profil && File::exists(public_path($kepsek->foto_profil))) {
                File::delete(public_path($kepsek->foto_profil));
            }

            // 2. Upload foto baru
            $file = $request->file('foto');
            $filename = 'kepsek_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kepsek'), $filename);
            
            $data['foto_profil'] = 'uploads/kepsek/' . $filename;
        }

        $kepsek->update($data);

        return redirect()->route('admin.kepala-sekolah.index')
                         ->with('success', 'Data akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kepsek = User::where('role', 'kepala_sekolah')->findOrFail($id);

        // Hapus foto profil saat akun dihapus
        if ($kepsek->foto_profil && File::exists(public_path($kepsek->foto_profil))) {
            File::delete(public_path($kepsek->foto_profil));
        }

        $kepsek->delete();

        return redirect()->back()->with('success', 'Akun Kepala Sekolah dihapus.');
    }
}