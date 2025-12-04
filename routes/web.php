<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DonaturController;
use App\Http\Controllers\LandingKegiatanController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KantorCabangController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TipeAbsensiController;
use App\Http\Controllers\TipeDonaturController;
use App\Http\Controllers\TransaksiController;
use App\Services\MenuService;

// Frontend Routes
Route::get('/', function () {
    return view('frontend.index');
})->name('frontend.index');

Route::get('/blog-grid', function () {
    return view('frontend.blog-grid');
})->name('frontend.blog-grid');

Route::get('/blog-single', function () {
    return view('frontend.blog-single');
})->name('frontend.blog-single');

Route::get('/signin', function () {
    return view('frontend.signin');
})->name('frontend.signin');

Route::get('/signup', function () {
    return view('frontend.signup');
})->name('frontend.signup');

Route::get('/404', function () {
    return view('frontend.404');
})->name('frontend.404');

// Admin API Routes - Public (must be before admin routes to avoid conflict)
Route::middleware(['web'])->prefix('admin/api')->group(function () {
    Route::get('/csrf-token', function () {
        return response()->json([
            'csrf_token' => csrf_token(),
        ]);
    })->name('admin.api.csrf-token');
    
    Route::post('/login', [LoginController::class, 'login'])->name('admin.api.login');
});

// Admin API Routes - Protected
Route::middleware(['web', 'auth'])->prefix('admin/api')->group(function () {
    Route::get('/menu', function () {
        return response()->json([
            'success' => true,
            'data' => MenuService::getFilteredMenu(),
        ]);
    })->name('admin.api.menu');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.api.logout');
    Route::get('/user', [LoginController::class, 'user'])->name('admin.api.user');
    
    // Dashboard API
    Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'stats'])->name('admin.api.dashboard.stats');
    
    // Landing Kegiatan API
    Route::apiResource('landing-kegiatan', LandingKegiatanController::class);
    
    // Jabatan API
    Route::apiResource('jabatan', JabatanController::class);
    Route::get('jabatan-permissions', [JabatanController::class, 'getPermissions'])->name('admin.api.jabatan.permissions');
    Route::post('jabatan/{id}/clone', [JabatanController::class, 'clone'])->name('admin.api.jabatan.clone');
    
    // Kantor Cabang API
    Route::apiResource('kantor-cabang', KantorCabangController::class);
    Route::get('kantor-cabang-next-kode', [KantorCabangController::class, 'getNextKode'])->name('admin.api.kantor-cabang.next-kode');
    
    // Tipe Absensi API
    Route::apiResource('tipe-absensi', TipeAbsensiController::class);
    Route::get('tipe-absensi-next-kode', [TipeAbsensiController::class, 'getNextKode'])->name('admin.api.tipe-absensi.next-kode');
    
    // Program API
    Route::apiResource('program', ProgramController::class);
    // Pangkat API
    Route::apiResource('pangkat', \App\Http\Controllers\PangkatController::class);
    // Gaji API
    Route::apiResource('gaji', \App\Http\Controllers\GajiController::class);
    
    // Karyawan API
    Route::get('karyawan-next-no-induk', [\App\Http\Controllers\KaryawanController::class, 'getNextNoInduk'])->name('admin.api.karyawan.next-no-induk');
    Route::apiResource('karyawan', \App\Http\Controllers\KaryawanController::class);

    // Mitra API
    Route::apiResource('mitra', MitraController::class);

    // Donatur API
    Route::get('donatur-next-kode', [DonaturController::class, 'getNextKode'])->name('admin.api.donatur.next-kode');
    Route::apiResource('donatur', DonaturController::class);
    
    // Search API
    Route::get('/search', [SearchController::class, 'search'])->name('admin.api.search');
    Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('admin.api.search.autocomplete');
     Route::apiResource('tipe-donatur', TipeDonaturController::class);

    // Transaksi API
    Route::apiResource('transaksi', TransaksiController::class);
});

// Admin Signin Route - Public (no auth required, must be before protected routes)
Route::get('/admin/signin', function () {
    return view('admin.index');
})->name('admin.signin');

// Admin Signup Route - Public (no auth required)
Route::get('/admin/signup', function () {
    return view('admin.index');
})->name('admin.signup');

// Admin Vue Routes - Protected by auth middleware
// Note: This route must come after /admin/signin and /admin/signup
// The {any} parameter will match everything except signin/signup because they're defined first
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/{any}', function () {
        return view('admin.index');
    })->where('any', '^(?!signin|signup).*$')->name('admin');
    
    // Root admin route (just /admin)
    Route::get('/admin', function () {
        return view('admin.index');
    })->name('admin.root');
});
