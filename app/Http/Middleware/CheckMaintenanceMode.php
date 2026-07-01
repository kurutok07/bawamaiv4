<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Ambil Status Maintenance
        $maintenance = DB::table('settings')->where('key', 'maintenance_mode')->value('value');

        // JIKA MAINTENANCE AKTIF ('1')
        if ($maintenance == '1') {
            
            // A. PENGECUALIAN ROUTE (Supaya Admin bisa Login)
            // Halaman Login, Logout, dan Landing Page tetap dibuka
            if ($request->is('/') || $request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // B. CEK USER LOGIN
            if (Auth::check()) {
                // Cek Role: HANYA 'ADMIN' YANG BOLEH LEWAT
                if (Auth::user()->role == 'admin') {
                    return $next($request);
                }
            }

            // C. SISANYA (Guru, Siswa, Kepsek, Tamu) -> TAMPILKAN 503
            // Pastikan view 'errors.maintenance' ada. 
            // Jika belum ada, ganti baris ini dengan: abort(503, 'Sedang Perbaikan');
            if (view()->exists('errors.maintenance')) {
                return response()->view('errors.maintenance', [], 503);
            }
            
            return response('<h1>Website Sedang Maintenance</h1><p>Hanya Administrator yang dapat mengakses.</p>', 503);
        }

        // Jika Maintenance MATI, lanjut normal
        return $next($request);
    }
}