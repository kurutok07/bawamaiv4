<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaportRedirectController extends Controller
{
    public function check()
    {
        // 1. Pastikan user sudah login (biasanya sudah dihandle middleware route)
        $user = Auth::user();

        // 2. Cek Role dan Arahkan (Sesuaikan dengan nama kolom role di database kamu)
        if ($user->role === 'admin') {
            return redirect()->route('admin.raport.index'); // Sesuaikan nama route admin
        } 
        elseif ($user->role === 'guru') {
            return redirect()->route('guru.raport.index'); // Route yang barusan kita percantik
        } 
        elseif ($user->role === 'siswa') {
            return redirect()->route('siswa.raport.index'); // Route siswa yang akan kita buat
        }

        // Default jika role tidak dikenali
        return redirect('/dashboard')->with('error', 'Role tidak dikenali.');
    }
}