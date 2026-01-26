<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

// --- 1. IMPORT CONTROLLERS ---
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\RaportRedirectController;
// --- TAMBAHAN PENTING ---
use App\Http\Controllers\SiswaRaportController; // <--- INI YG BIKIN ERROR SEBELUMNYA
use App\Http\Controllers\RaportGuruController;  // <--- Tambahkan ini agar tidak perlu tulis path panjang di bawah
use App\Http\Controllers\RaportCategoryController; // <--- Tambahkan ini juga
// ------------------------

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 2. PUBLIC ROUTES (Landing Page) ---
Route::get('/', [LandingPageController::class, 'index'])->name('landing');


// --- 3. GUEST ROUTES (Belum Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
});

// --- 4. AUTH ROUTES (Harus Login) ---
Route::middleware(['auth'])->group(function () {
    
    // A. Dashboard & Logout (Semua User Login Boleh Akses)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // B. AKSES LMS (Semua User Boleh Baca Materi)
    Route::get('/lms/{slug}', [LmsController::class, 'show'])->name('lms.show');

    // Logic Redirect Akses Raport
    Route::get('/akses-raport', function () {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // UBAH DARI 'admin.raport.index' MENJADI 'admin.raport-categories.index'
            return redirect()->route('admin.raport-categories.index');
        } elseif ($user->role === 'guru') {
            return redirect()->route('guru.raport.index'); 
        } elseif ($user->role === 'siswa') {
            return redirect()->route('siswa.raport.index'); 
        }

        return redirect('/dashboard')->with('error', 'Akses E-Raport tidak ditemukan untuk role Anda.');

    })->middleware(['auth', 'verified'])->name('akses.raport');

    // ==========================================================
    // C. GROUP STRICT SISWA (Hanya Role: 'siswa')
    // ==========================================================
    Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        // Halaman Utama Raport Siswa
        // SEKARANG INI AKAN BERHASIL KARENA SUDAH DI-IMPORT DI ATAS
        Route::get('/raport', [SiswaRaportController::class, 'index'])->name('raport.index');
        Route::get('/raport/lihat/{kelas_id}/{semester}', [SiswaRaportController::class, 'show'])
        ->name('raport.show');
    });

    // ==========================================================
    // D. GROUP STRICT ADMIN (Hanya Role: 'admin')
    // ==========================================================
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        
        Route::post('/tahun-ajaran/{id}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');
        Route::resource('tahun-ajaran', TahunAjaranController::class);

        Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import');
        Route::resource('guru', GuruController::class);

        Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
        Route::resource('siswa', SiswaController::class);

        Route::post('/kelas/{id}/add-siswa', [KelasController::class, 'addSiswa'])->name('kelas.addSiswa');
        Route::delete('/kelas/{id_kelas}/remove-siswa/{id_siswa}', [KelasController::class, 'removeSiswa'])->name('kelas.removeSiswa');
        Route::post('/kelas/{id}/import-siswa', [KelasController::class, 'importSiswa'])->name('kelas.importSiswa');
        Route::resource('kelas', KelasController::class);
        // Kategori Raport
        Route::resource('raport-categories', RaportCategoryController::class)
    ->names('admin.raport-categories');
        
    });

    // ==========================================================
    // E. GROUP STRICT GURU (Hanya Role: 'guru')
    // ==========================================================
    Route::middleware(['role:guru'])->prefix('guru')->group(function() {
        
        // Route Halaman Utama Pilih Kelas
        Route::get('/raport', [RaportGuruController::class, 'index'])->name('guru.raport.index');
        
        // Route Detail Kelas & Upload
        Route::get('/raport/kelas/{kelas_id}', [RaportGuruController::class, 'show'])->name('guru.raport.show');
        
        // Route Proses Upload & Delete
        Route::post('/raport/upload', [RaportGuruController::class, 'store'])->name('guru.raport.store');
        Route::delete('/raport/{id}', [RaportGuruController::class, 'destroy'])->name('guru.raport.destroy');
        
    });

    // ==========================================================
    // F. GROUP MIXED (Boleh 'admin' DAN 'guru')
    // ==========================================================
    Route::middleware(['role:admin,guru'])->prefix('admin')->group(function () {
        
        // Analytics
        Route::get('/analytics', [LmsController::class, 'analytics'])->name('admin.analytics');
        Route::get('/analytics/students', [LmsController::class, 'getStudents'])->name('admin.analytics.students');
        Route::get('/analytics/student-history/{id}', [LmsController::class, 'getStudentHistory'])->name('admin.analytics.history');

        // Manajemen Materi LMS 
        Route::resource('lms-items', LmsController::class)->except(['show']);
    });

});


// --- 5. UTILITY ROUTES (Storage Bypass) ---
Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $path = storage_path("app/public/{$folder}/{$filename}");

    if (!file_exists($path)) {
        $placeholder = public_path('img/no-image.png');
        if (!file_exists($placeholder)) {
            abort(404);
        }
        return response()->file($placeholder);
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return Response::make($file, 200, [
        'Content-Type' => $type,
        'Cache-Control' => 'public, max-age=86400',
    ]);
});