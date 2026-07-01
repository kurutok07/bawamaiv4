<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;
use Illuminate\Pagination\Paginator; // <--- JANGAN LUPA INI! Kalau lupa, pasti crash.

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- TAMBAHKAN INI ---
        // Setiap kali ada event Login, jalankan listener LogSuccessfulLogin
	Paginator::useBootstrapFive();        
	Event::listen(
            Login::class,
            LogSuccessfulLogin::class,
        );
    }
}
