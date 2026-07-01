<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Alias (Untuk middleware yang dipanggil spesifik di route, misal: 'role:admin')
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);


        // 2. Global Middleware (Jalan di SEMUA request/halaman)
        // Kita taruh CheckMaintenanceMode di sini agar website bisa dikunci total saat maintenance
$middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();