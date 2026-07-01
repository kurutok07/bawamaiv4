<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

// --- CONTROLLERS ---
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;

// Master Data Controllers
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KepalaSekolahController;
use App\Http\Controllers\Admin\AccountLainnyaController;

// Feature Controllers
use App\Http\Controllers\LmsController;
use App\Http\Controllers\SiswaHealthController;
use App\Http\Controllers\SiswaRaportController;
use App\Http\Controllers\RaportGuruController;
use App\Http\Controllers\RaportCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes - SYSTEM MAP
|--------------------------------------------------------------------------
*/

// ==========================================================
// 1. PUBLIC & GUEST ROUTES
// ==========================================================

// Landing Page
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
});

// Storage Bypass (Untuk menampilkan gambar di hosting shared)
Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $pathUploads = public_path("uploads/{$folder}/{$filename}");
    if (file_exists($pathUploads)) return response()->file($pathUploads);

    $pathStorage = storage_path("app/public/{$folder}/{$filename}");
    if (file_exists($pathStorage)) return response()->file($pathStorage);

    $placeholder = public_path('img/no-image.png');
    return file_exists($placeholder) ? response()->file($placeholder) : abort(404);
});


// ==========================================================
// 2. AUTHENTICATED ROUTES (Harus Login)
// ==========================================================
Route::middleware(['auth'])->group(function () {
    
    // --- COMMON ACCESS (Dashboard, Profile, Logout) ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/profile/update-foto', [ProfileController::class, 'updateFoto'])->name('profile.updateFoto');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/profile/update-portofolio', [ProfileController::class, 'updatePortofolio'])->name('profile.updatePortofolio');
    // --- VIEW LMS (Semua user boleh BACA materi) ---
    Route::get('/lms/{slug}', [LmsController::class, 'show'])->name('lms.show');

    // --- RAPORT REDIRECTOR ---
    Route::get('/akses-raport', function () {
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.raport-categories.index');
        if ($user->role === 'guru') return redirect()->route('guru.raport.index');
        if ($user->role === 'siswa') return redirect()->route('siswa.raport.index');
        return redirect('/dashboard')->with('error', 'Akses E-Raport tidak ditemukan.');
    })->middleware(['verified'])->name('akses.raport');

    // ==========================================================
    // A. AREA SISWA
    // ==========================================================
    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/raport', [SiswaRaportController::class, 'index'])->name('raport.index');
        Route::get('/raport/lihat/{kelas_id}/{semester?}', [SiswaRaportController::class, 'show'])->name('raport.show');
    });

    // ==========================================================
    // B. AREA GURU
    // ==========================================================
    Route::middleware(['role:guru'])->prefix('guru')->group(function() {
        Route::get('/raport', [RaportGuruController::class, 'index'])->name('guru.raport.index');
        Route::get('/raport/kelas/{kelas_id}', [RaportGuruController::class, 'show'])->name('guru.raport.show');
        Route::post('/raport/upload', [RaportGuruController::class, 'store'])->name('guru.raport.store');
        Route::delete('/raport/{id}', [RaportGuruController::class, 'destroy'])->name('guru.raport.destroy');
    });

    // ==========================================================
    // C. AREA ADMIN & MANAGEMENT (Terpusat di Prefix 'admin')
    // ==========================================================
    Route::prefix('admin')->group(function () {

        // ------------------------------------------------------
        // C1. SUPER ADMIN ONLY (Settings, Accounts, Master Data)
        // ------------------------------------------------------
        Route::middleware(['role:admin'])->group(function () {
            
            // Pengaturan & Maintenance
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('/settings/maintenance', [SettingController::class, 'updateMaintenance'])->name('settings.maintenance');
            Route::post('/settings/carousel', [SettingController::class, 'storeCarousel'])->name('settings.carousel.store');
            Route::delete('/settings/carousel/{id}', [SettingController::class, 'destroyCarousel'])->name('settings.carousel.delete');

            // Manajemen Akun (Pegawai & Yayasan)
            Route::resource('accounts-others', AccountLainnyaController::class)->names('accounts');
            Route::resource('kepala-sekolah', KepalaSekolahController::class)->names('admin.kepala-sekolah');

            // Master Data (Tahun Ajaran, Guru, Kategori Raport)
            Route::post('/tahun-ajaran/{id}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');
            Route::resource('tahun-ajaran', TahunAjaranController::class);
            Route::delete('/siswa/destroy-all', [SiswaController::class, 'destroyAll'])->name('siswa.destroyAll');
            Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import');
            Route::delete('/guru/destroy-all', [GuruController::class, 'destroyAll'])->name('guru.destroyAll');
        // Gunakan 'except' untuk membuang index & show (karena sudah dibuat manual di atas)
            Route::resource('guru', GuruController::class)->except(['index', 'show']);
            Route::resource('raport-categories', RaportCategoryController::class)->names('admin.raport-categories');

            // Manajemen Kelas (Logic Berat)
            Route::post('/kelas/generate', [KelasController::class, 'copyClasses'])->name('kelas.generate');
            Route::post('/kelas/{id}/add-siswa', [KelasController::class, 'addSiswa'])->name('kelas.addSiswa');
            Route::delete('/kelas/{id_kelas}/remove-siswa/{id_siswa}', [KelasController::class, 'removeSiswa'])->name('kelas.removeSiswa');
            Route::post('/kelas/{id}/import-siswa', [KelasController::class, 'importSiswa'])->name('kelas.importSiswa');
            Route::post('/kelas/{id}/add-guru', [KelasController::class, 'addGuru'])->name('kelas.addGuru');
            Route::delete('/kelas/{id_kelas}/remove-guru/{id_guru}', [KelasController::class, 'removeGuru'])->name('kelas.removeGuru');
            Route::resource('kelas', KelasController::class);
        });
        Route::middleware(['role:admin,kepala_sekolah'])->group(function () {
            Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
            Route::get('/guru/{guru}', [GuruController::class, 'show'])->name('guru.show');
        });
        // ------------------------------------------------------
        // C2. MANAJEMEN DATA SISWA (Complex Logic: Admin, UKS, Kepsek)
        // ------------------------------------------------------
        
        // >> Rute Eksekusi (HANYA ADMIN) - Wajib ditaruh DI ATAS Rute View
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
            Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
            Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
            Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
            Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
            Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
        });

        // >> Rute View/Lihat (ADMIN, UKS, KEPSEK)
        Route::middleware(['role:admin,uks,kepala_sekolah'])->group(function () {
            Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
            Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');
        });

        // ------------------------------------------------------
        // C3. MANAJEMEN KESEHATAN / UKS (Admin & UKS)
        // ------------------------------------------------------
        Route::middleware(['role:admin,uks'])->prefix('siswa-health')->group(function () {
            Route::post('/log-store', [SiswaHealthController::class, 'storeLog'])->name('siswa.health-log.store');
            Route::post('/profile-update/{siswa_id}', [SiswaHealthController::class, 'updateProfile'])->name('siswa.health-profile.update');
            Route::put('/log-update/{id}', [SiswaHealthController::class, 'updateLog'])->name('siswa.health-log.update');
            Route::delete('/log-destroy/{id}', [SiswaHealthController::class, 'destroyLog'])->name('siswa.health-log.destroy');
        });

        // ------------------------------------------------------
        // C4. LMS & MATERI (Admin, Guru, & PERPUS)
        // ------------------------------------------------------
        // Menambahkan 'perpus' agar bisa CRUD materi LMS sepenuhnya
        Route::middleware(['role:admin,guru,perpus,kepala_sekolah,admin_qurana'])->group(function () {
            
            // Resource LMS (Kecuali Show karena Show publik)
            Route::resource('lms-items', LmsController::class)->except(['show']);
            
            // Analytics Siswa (Bisa dilihat Admin, Guru, Perpus, Kepsek)
            Route::get('/analytics', [LmsController::class, 'analytics'])->name('admin.analytics');
            Route::get('/analytics/students', [LmsController::class, 'getStudents'])->name('admin.analytics.students');
            Route::get('/analytics/student-history/{id}', [LmsController::class, 'getStudentHistory'])->name('admin.analytics.history');
            Route::get('/analytics/student-detail/{id}', [LmsController::class, 'getStudentDetail'])->name('admin.analytics.student-detail');
        });

        // ------------------------------------------------------
        // C5. MONITORING GURU (Admin & Kepsek)
        // ------------------------------------------------------
        Route::middleware(['role:admin,kepala_sekolah'])->group(function () {
            Route::get('/analytics/teachers', [LmsController::class, 'teacherAnalytics'])->name('admin.analytics.teachers');
            Route::get('/analytics/teacher-detail/{id}', [LmsController::class, 'getTeacherDetail'])->name('admin.analytics.teacher-detail');
        });

    }); // End Admin Prefix

}); // End Auth Middleware