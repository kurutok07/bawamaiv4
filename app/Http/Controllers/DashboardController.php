<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        // Cek Role dan arahkan ke view yang sesuai
        if ($role == 'admin') {
            return view('dashboard.admin');
        } 
        elseif ($role == 'guru') {
            return view('dashboard.guru');
        } 
        elseif ($role == 'siswa') {
            return redirect()->route("landing");
        } 
        elseif ($role == 'kepala_sekolah') {
            return view('dashboard.kepala_sekolah');
        }
        else {
            // Fallback jika role tidak dikenali
            return abort(403, 'Role tidak dikenali');
        }
    }
}