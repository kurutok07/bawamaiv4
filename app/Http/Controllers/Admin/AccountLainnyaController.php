<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountLainnyaController extends Controller
{
    public function index()
    {
        // Update query: Ambil juga role 'kepala_sekolah' dan 'yayasan' (jika ada)
        // Kita gunakan whereIn agar mencakup semua role non-admin/guru/siswa
        $users = User::whereIn('role', ['perpus', 'uks', 'kepala_sekolah', 'yayasan', 'admin_qurana'])
                     ->latest()
                     ->get();
                     
        return view('admin.accounts.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email'    => 'nullable|email|unique:users,email',
            // Validasi menerima 'yayasan' & 'kepala_sekolah' dari form
            'role'     => 'required|in:perpus,uks,kepala_sekolah,yayasan,admin_qurana', 
            'password' => 'required|min:6',
        ]);

        // LOGIC KHUSUS: 
        // Jika inputnya 'yayasan', ubah jadi 'kepala_sekolah' saat disimpan ke DB
        // Agar permission/middleware otomatis menganggapnya setara Kepala Sekolah
        $roleToSave = ($request->role === 'yayasan') ? 'kepala_sekolah' : $request->role;

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'role'     => $roleToSave, // Simpan role hasil konversi
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Akun berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'email'    => 'nullable|email|unique:users,email,' . $id,
            'role'     => 'required|in:perpus,uks,kepala_sekolah,yayasan,admin_qurana',
            'password' => 'nullable|min:6',
        ]);

        // LOGIC KHUSUS UPDATE:
        // Sama seperti store, konversi yayasan -> kepala_sekolah
        $roleToSave = ($request->role === 'yayasan') ? 'kepala_sekolah' : $request->role;

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'role'     => $roleToSave,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Data akun berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Sedikit validasi agar tidak bisa hapus diri sendiri (safety)
        if ($user->id == auth()->id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Akun telah dihapus!');
    }
}