<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed ...$roles  (Daftar role yang diizinkan, dipisah koma)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek Login (Jaga-jaga)
        if (!Auth::check()) {
            return redirect('login');
        }

        // 2. Cek apakah Role User ada di dalam daftar yang diizinkan
        // $request->user()->role mengambil kolom 'role' dari tabel users
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // 3. Jika tidak cocok, tolak akses (403 Forbidden)
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}