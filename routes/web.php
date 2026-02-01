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
use App\Http\Controllers\SiswaRaportController;
use App\Http\Controllers\RaportGuruController;
use App\Http\Controllers\RaportCategoryController;
use App\Http\Controllers\ProfileController;

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
    
    // A. Dashboard, Logout & Profile (Semua User Login Boleh Akses)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/profile/update-foto', [ProfileController::class, 'updateFoto'])->name('profile.updateFoto');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // B. AKSES LMS (Semua User Boleh Baca Materi)
    Route::get('/lms/{slug}', [LmsController::class, 'show'])->name('lms.show');

    // Logic Redirect Akses Raport
    Route::get('/akses-raport', function () {
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.raport-categories.index');
        if ($user->role === 'guru') return redirect()->route('guru.raport.index');
        if ($user->role === 'siswa') return redirect()->route('siswa.raport.index');
        return redirect('/dashboard')->with('error', 'Akses E-Raport tidak ditemukan.');
    })->middleware(['verified'])->name('akses.raport');

    // ==========================================================
    // C. GROUP STRICT SISWA (Hanya Role: 'siswa')
    // ==========================================================
    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/raport', [SiswaRaportController::class, 'index'])->name('raport.index');
        Route::get('/raport/lihat/{kelas_id}/{semester}', [SiswaRaportController::class, 'show'])->name('raport.show');
    });

    // ==========================================================
    // D. GROUP STRICT ADMIN (Hanya Role: 'admin')
    // ==========================================================
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        
        // Master Data
        Route::resource('kepala-sekolah', \App\Http\Controllers\KepalaSekolahController::class)
         ->names('admin.kepala-sekolah');
        Route::post('/tahun-ajaran/{id}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');
        Route::resource('tahun-ajaran', TahunAjaranController::class);

        Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import');
        Route::resource('guru', GuruController::class);

        Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
        Route::resource('siswa', SiswaController::class);
        
        // Manajemen Kelas (Input/Edit Kelas)
        Route::post('/kelas/{id}/add-siswa', [KelasController::class, 'addSiswa'])->name('kelas.addSiswa');
        Route::delete('/kelas/{id_kelas}/remove-siswa/{id_siswa}', [KelasController::class, 'removeSiswa'])->name('kelas.removeSiswa');
        Route::post('/kelas/{id}/import-siswa', [KelasController::class, 'importSiswa'])->name('kelas.importSiswa');
        Route::post('/kelas/{id}/add-guru', [KelasController::class, 'addGuru'])->name('kelas.addGuru');
        Route::delete('/kelas/{id_kelas}/remove-guru/{id_guru}', [KelasController::class, 'removeGuru'])->name('kelas.removeGuru');
        
        // FITUR BARU: GENERATE KELAS
        Route::post('/kelas/generate', [KelasController::class, 'copyClasses'])->name('kelas.generate');
        Route::resource('kelas', KelasController::class);
        
        // Kategori Raport
        Route::resource('raport-categories', RaportCategoryController::class)->names('admin.raport-categories');
    });

    // ==========================================================
    // G. GROUP MONITORING (Admin & Kepala Sekolah)
    // ==========================================================
    Route::middleware(['role:admin,kepala_sekolah'])->prefix('admin')->group(function () {
        // Analytics Guru (Kinerja Guru)
        Route::get('/analytics/teachers', [LmsController::class, 'teacherAnalytics'])->name('admin.analytics.teachers');
        Route::get('/analytics/teacher-detail/{id}', [LmsController::class, 'getTeacherDetail'])->name('admin.analytics.teacher-detail');
    });

    // ==========================================================
    // E. GROUP STRICT GURU (Hanya Role: 'guru')
    // ==========================================================
    Route::middleware(['role:guru'])->prefix('guru')->group(function() {
        Route::get('/raport', [RaportGuruController::class, 'index'])->name('guru.raport.index');
        Route::get('/raport/kelas/{kelas_id}', [RaportGuruController::class, 'show'])->name('guru.raport.show');
        Route::post('/raport/upload', [RaportGuruController::class, 'store'])->name('guru.raport.store');
        Route::delete('/raport/{id}', [RaportGuruController::class, 'destroy'])->name('guru.raport.destroy');
    });

    // ==========================================================
    // F. GROUP MIXED (Admin, Guru, & Kepala Sekolah)
    // ==========================================================
    Route::middleware(['role:admin,guru,kepala_sekolah'])->prefix('admin')->group(function () {
        
        // Analytics Siswa
        Route::get('/analytics', [LmsController::class, 'analytics'])->name('admin.analytics');
        Route::get('/analytics/students', [LmsController::class, 'getStudents'])->name('admin.analytics.students');
        Route::get('/analytics/student-history/{id}', [LmsController::class, 'getStudentHistory'])->name('admin.analytics.history');
        Route::get('/analytics/student-detail/{id}', [LmsController::class, 'getStudentDetail'])->name('admin.analytics.student-detail');
        
        // Manajemen LMS (Materi)
        // Kepsek bisa lihat list materi (index), tapi create/edit dibatasi di view logic saja atau biarkan terbuka
        Route::resource('lms-items', LmsController::class)->except(['show']);
    });

});

// --- 5. UTILITY ROUTES (Storage Bypass) ---
Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    // Cek path uploads (untuk foto profil)
    $pathUploads = public_path("uploads/{$folder}/{$filename}");
    if (file_exists($pathUploads)) {
        return response()->file($pathUploads);
    }

    // Cek path storage (untuk file lain)
    $pathStorage = storage_path("app/public/{$folder}/{$filename}");
    if (file_exists($pathStorage)) {
        return response()->file($pathStorage);
    }

    // Fallback Image
    $placeholder = public_path('img/no-image.png');
    return file_exists($placeholder) ? response()->file($placeholder) : abort(404);
});