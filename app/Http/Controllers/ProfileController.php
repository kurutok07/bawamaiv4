<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\Siswa;
use App\Models\Guru;

class ProfileController extends Controller
{
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        
        // Variabel dinamis untuk menampung target
        $targetModel = null;
        $folder = 'uploads/others';
        $columnName = 'foto'; // Default column name

        // 1. Tentukan Logika Berdasarkan Role
        if ($user->role == 'siswa') {
            // SISWA: Simpan di tabel 'siswas', kolom 'foto'
            $targetModel = Siswa::where('user_id', $user->id)->first();
            $folder = 'uploads/siswa';
            $columnName = 'foto';

        } elseif ($user->role == 'guru') {
            // GURU: Simpan di tabel 'gurus', kolom 'foto'
            $targetModel = Guru::where('user_id', $user->id)->first();
            $folder = 'uploads/guru';
            $columnName = 'foto';

        } elseif ($user->role == 'kepala_sekolah' || $user->role == 'admin') {
            // KEPSEK & ADMIN: Simpan langsung di tabel 'users', kolom 'foto_profil'
            $targetModel = $user; // Targetnya adalah user itu sendiri
            $folder = 'uploads/kepsek';
            $columnName = 'foto_profil';
        }

        // 2. Eksekusi Upload jika Target Valid
        if ($targetModel) {
            if ($request->hasFile('foto')) {
                
                // A. Pastikan Folder Ada
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }

                // B. Hapus foto lama (Ambil nama file dari kolom dinamis)
                $oldFile = $targetModel->$columnName; 
                if ($oldFile && File::exists(public_path($oldFile))) {
                    File::delete(public_path($oldFile));
                }

                // C. Upload foto baru
                $file = $request->file('foto');
                // Penamaan file unik
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($path, $filename);

                // D. Simpan path ke Database (Kolom Dinamis)
                $targetModel->$columnName = $folder . '/' . $filename;
                $targetModel->save();
            }
            return back()->with('success', 'Foto profil berhasil diperbarui.');
        }

        return back()->with('error', 'Data profil tidak ditemukan.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah!']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}