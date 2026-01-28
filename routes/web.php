<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DonaturController;
use App\Http\Controllers\LandingKegiatanController;
use App\Http\Controllers\LandingProposalController;
use App\Http\Controllers\LandingBulletinController;
use App\Http\Controllers\JabatanController;
use App\Models\LandingBulletin;
use App\Models\LandingProgram;
use App\Models\LandingKegiatan;
use App\Models\LandingProfile;
use App\Http\Controllers\KantorCabangController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TipeAbsensiController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TipeDonaturController;
use App\Http\Controllers\TipeProgramController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PengajuanDanaController;
use App\Services\MenuService;

// Serve SVG favicon at /favicon.ico for browsers requesting that path and force revalidation
Route::get('/favicon.ico', function () {
    $path = public_path('favicon.svg');
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $mtime = filemtime($path);
        return response($content, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Last-Modified', gmdate('D, d M Y H:i:s', $mtime) . ' GMT')
            ->header('ETag', md5($content));
    }
    abort(404);
});

// Frontend Routes
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $landingProfile = Schema::hasTable('landing_profiles') ? LandingProfile::first() : null;
    $landingBulletins = LandingBulletin::orderByDesc('date')->limit(6)->get();
    $landingBulletinsTotal = Schema::hasTable('landing_bulletins') ? LandingBulletin::count() : 0;
    
    return view('frontend.index', compact('landingProfile', 'landingBulletins', 'landingBulletinsTotal'));
})->name('frontend.index');

// Tentang Kami page
Route::get('/tentang-kami', function () {
    $landingProfile = Schema::hasTable('landing_profiles') ? LandingProfile::first() : null;
    return view('frontend.tentang-kami', compact('landingProfile'));
})->name('frontend.tentang-kami');

// Program page
Route::get('/program', function () {
    $landingPrograms = LandingProgram::orderByDesc('created_at')->limit(12)->get();
    $landingProgramsTotal = Schema::hasTable('landing_programs') ? LandingProgram::count() : 0;
    $landingProfile = Schema::hasTable('landing_profiles') ? LandingProfile::first() : null;
    return view('frontend.program', compact('landingPrograms', 'landingProgramsTotal', 'landingProfile'));
})->name('frontend.program');

// Cara Donasi page
Route::get('/cara-donasi', function () {
    $landingProfile = Schema::hasTable('landing_profiles') ? LandingProfile::first() : null;
    return view('frontend.cara-donasi', compact('landingProfile'));
})->name('frontend.cara-donasi');

// Galeri page
Route::get('/galeri', function () {
    $landingKegiatan = LandingKegiatan::orderByDesc('activity_date')->limit(12)->get();
    $landingKegiatanTotal = Schema::hasTable('landing_kegiatans') ? LandingKegiatan::count() : 0;
    $landingProfile = Schema::hasTable('landing_profiles') ? LandingProfile::first() : null;
    return view('frontend.galeri', compact('landingKegiatan', 'landingKegiatanTotal', 'landingProfile'));
})->name('frontend.galeri');

// Transparansi page
Route::get('/transparansi', function () {
    $kantorCabangCount = Schema::hasTable('kantor_cabang') ? DB::table('kantor_cabang')->count() : 0;
    $donaturCount = Schema::hasTable('donaturs') ? DB::table('donaturs')->count() : 0;
    
    $fundraiserCount = 0;
    if (Schema::hasTable('model_has_roles') && Schema::hasTable('roles')) {
        $fundraiserCount = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->whereRaw('LOWER(roles.name) = ?', ['fundrising'])
            ->count();
    }
    
    $penggalanganDanaCount = Schema::hasTable('transaksis') ? DB::table('transaksis')->count() : 0;
    $landingProfile = Schema::hasTable('landing_profiles') ? LandingProfile::first() : null;
    
    return view('frontend.transparansi', compact(
        'kantorCabangCount', 'donaturCount', 'fundraiserCount', 'penggalanganDanaCount', 'landingProfile'
    ));
})->name('frontend.transparansi');

// Kontak page
Route::get('/kontak', function () {
    $landingProfile = Schema::hasTable('landing_profiles') ? LandingProfile::first() : null;
    return view('frontend.kontak', compact('landingProfile'));
})->name('frontend.kontak');

Route::get('/blog-grid', function () {
    return view('frontend.blog-grid');
})->name('frontend.blog-grid');

// Program detail route (public)
Route::get('/program/{slug}', function ($slug) {
    $program = \App\Models\LandingProgram::where('slug', $slug)->first();
    if (! $program && is_numeric($slug)) {
        $program = \App\Models\LandingProgram::find($slug);
    }
    if (! $program) {
        $program = \App\Models\LandingProgram::get()->first(function ($item) use ($slug) {
            return \Illuminate\Support\Str::slug($item->name) === $slug;
        });
    }
    if (! $program) abort(404);
    return view('frontend.program-single', compact('program'));
})->name('frontend.program-single');

Route::get('/kegiatan/{slug}', function ($slug) {
    $kegiatan = null;

    // Try finding by explicit slug column
    $kegiatan = \App\Models\LandingKegiatan::where('slug', $slug)->first();

    // Fallback: if slug looks numeric, try as ID
    if (! $kegiatan && is_numeric($slug)) {
        $kegiatan = \App\Models\LandingKegiatan::find($slug);
    }

    // Final fallback: match by slugified title (best-effort)
    if (! $kegiatan) {
        $kegiatan = \App\Models\LandingKegiatan::get()->first(function ($item) use ($slug) {
            return \Illuminate\Support\Str::slug($item->title) === $slug;
        });
    }

    if (! $kegiatan) {
        abort(404);
    }

    return view('frontend.blog-single', compact('kegiatan'));
})->name('frontend.blog-single');

// robots.txt
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Sitemap: ' . url('/sitemap.xml'),
    ];
    return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
});

// sitemap.xml - simple dynamic sitemap for homepage, programs, kegiatan
Route::get('/sitemap.xml', function () {
    $urls = [];
    $urls[] = ['loc' => url('/'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '1.0'];

    if (Schema::hasTable('landing_programs')) {
        foreach (\App\Models\LandingProgram::orderByDesc('updated_at')->get() as $p) {
            $urls[] = ['loc' => url('/program/' . ($p->slug ?? \Illuminate\Support\Str::slug($p->name))), 'lastmod' => optional($p->updated_at)->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.8'];
        }
    }

    if (Schema::hasTable('landing_kegiatan')) {
        foreach (\App\Models\LandingKegiatan::orderByDesc('activity_date')->get() as $k) {
            $urls[] = ['loc' => url('/kegiatan/' . ($k->slug ?? \Illuminate\Support\Str::slug($k->title))), 'lastmod' => optional($k->updated_at ?? $k->activity_date)->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.8'];
        }
    }

    $content = view('sitemap_xml', compact('urls'))->render();
    return response($content, 200, ['Content-Type' => 'application/xml']);
});

// Contact form endpoint (frontend)
Route::post('/contact', [ContactController::class, 'store'])->name('frontend.contact.store');

// Public API for frontend landing bulletins (used by home page "Load more")
Route::get('/api/landing-bulletins', [\App\Http\Controllers\LandingBulletinController::class, 'publicIndex']);
Route::get('/api/landing-programs', [\App\Http\Controllers\LandingProgramController::class, 'publicIndex']);
Route::get('/api/landing-kegiatan', [\App\Http\Controllers\LandingKegiatanController::class, 'publicIndex']);

// Frontend auth pages removed (Sign In / Sign Up)

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
    
    // Debug endpoint to check auth status
    Route::get('/debug-auth', function () {
        return response()->json([
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
        ]);
    });
});

// Admin API Routes - Protected
Route::middleware(['web', 'auth'])->prefix('admin/api')->group(function () {
    Route::get('/menu', function () {
        return response()->json([
            'success' => true,
            'data' => MenuService::getFilteredMenu(),
        ]);
    })->name('admin.api.menu');
    
    // Debug endpoint to check user permissions
    Route::get('/debug-permissions', function () {
        $user = auth()->user();
        $user->load('roles.permissions');
        
        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'roles' => $user->roles->pluck('name'),
            'all_permissions' => $user->getAllPermissions()->pluck('name'),
            'can_view_karyawan' => $user->can('view karyawan'),
            'can_view_mitra' => $user->can('view mitra'),
            'can_view_donatur' => $user->can('view donatur'),
        ]);
    })->name('admin.api.debug-permissions');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.api.logout');
    Route::get('/user', [LoginController::class, 'user'])->name('admin.api.user');
    Route::post('/user/avatar', [LoginController::class, 'avatar'])->name('admin.api.user.avatar');
    Route::post('/user/social', [LoginController::class, 'updateSocial'])->name('admin.api.user.social');
    // Update authenticated user's personal information
    Route::post('/user/profile', [LoginController::class, 'updateProfile'])->name('admin.api.user.profile');
    Route::post('/user/password', [LoginController::class, 'changePassword'])->name('admin.api.user.password');
    
    // Dashboard API
    Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'stats'])->name('admin.api.dashboard.stats');
    
    // Landing Kegiatan API
    Route::apiResource('landing-kegiatan', LandingKegiatanController::class);
    // Landing Program API
    Route::apiResource('landing-program', \App\Http\Controllers\LandingProgramController::class);
        Route::apiResource('landing-proposal', LandingProposalController::class);
    Route::apiResource('landing-bulletin', LandingBulletinController::class);
    
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

    // Tipe Program API
    Route::apiResource('tipe-program', TipeProgramController::class);
    
    // Program API
    // Program balance endpoint (per-month allocation/balance)
    Route::get('program/{id}/balance', [ProgramController::class, 'balance']);
    // Aggregate balance across all programs for submission type
    Route::get('programs/aggregate-balance', [ProgramController::class, 'aggregateBalance']);
    // Program shares summary for Keuangan grid
    Route::get('keuangan/program-shares-summary', [ProgramController::class, 'programSharesSummary']);
    Route::apiResource('program', ProgramController::class);
    // Program share types (for frontend dynamic shares)
    // IMPORTANT: Specific routes MUST come before general routes
    Route::get('program-share-types/submission-types', [\App\Http\Controllers\ProgramShareTypeController::class, 'submissionTypes']);
    Route::get('program-share-types', [\App\Http\Controllers\ProgramShareTypeController::class, 'index']);
    // Pangkat API
    Route::apiResource('pangkat', \App\Http\Controllers\PangkatController::class);
    // Gaji API
    Route::apiResource('gaji', \App\Http\Controllers\GajiController::class);

    // Payroll / Rekap Gaji per Periode
    Route::get('operasional/payroll/periods', [\App\Http\Controllers\Api\PayrollController::class, 'index']);
    Route::post('operasional/payroll/periods/generate', [\App\Http\Controllers\Api\PayrollController::class, 'generate']);
    Route::get('operasional/payroll/periods/missing-count', [\App\Http\Controllers\Api\PayrollController::class, 'missingCount']);
    Route::get('operasional/payroll/periods/{id}', [\App\Http\Controllers\Api\PayrollController::class, 'show']);
    Route::post('operasional/payroll/periods/{id}/transfer', [\App\Http\Controllers\Api\PayrollController::class, 'transferPeriod']);

    // Return the current authenticated user's latest payroll period + record
    Route::get('operasional/payroll/me', [\App\Http\Controllers\Api\PayrollController::class, 'me']);

    // Return ALL payroll records for the current authenticated user
    Route::get('operasional/payroll/me/list', [\App\Http\Controllers\Api\PayrollController::class, 'myRecords']);

    // Record and items endpoints
    Route::get('operasional/payroll/periods/{periodId}/records/{recordId}', [\App\Http\Controllers\Api\PayrollController::class, 'showRecord']);
    Route::post('operasional/payroll/periods/{periodId}/records/{recordId}/items', [\App\Http\Controllers\Api\PayrollController::class, 'addItem']);
    Route::put('operasional/payroll/periods/{periodId}/records/{recordId}/items/{itemId}', [\App\Http\Controllers\Api\PayrollController::class, 'updateItem']);
    Route::delete('operasional/payroll/periods/{periodId}/records/{recordId}/items/{itemId}', [\App\Http\Controllers\Api\PayrollController::class, 'deleteItem']);
    // Update record (status, notes)
    Route::put('operasional/payroll/periods/{periodId}/records/{recordId}', [\App\Http\Controllers\Api\PayrollController::class, 'updateRecord']);

    // Admin: upload/replace/delete transfer proof for a record
    Route::post('operasional/payroll/periods/{periodId}/records/{recordId}/transfer-proof', [\App\Http\Controllers\Api\PayrollController::class, 'uploadTransferProof']);
    Route::delete('operasional/payroll/periods/{periodId}/records/{recordId}/transfer-proof', [\App\Http\Controllers\Api\PayrollController::class, 'deleteTransferProof']);

    // Karyawan API
    Route::get('karyawan-next-no-induk', [\App\Http\Controllers\KaryawanController::class, 'getNextNoInduk'])->name('admin.api.karyawan.next-no-induk');
    Route::apiResource('karyawan', \App\Http\Controllers\KaryawanController::class);

    // Notifications
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('admin.api.notifications.index');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('admin.api.notifications.read');
    Route::post('notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('admin.api.notifications.readAll');

    // Mitra API
    Route::get('check-email', [MitraController::class, 'checkEmail'])->name('admin.api.check-email');
    Route::apiResource('mitra', MitraController::class);
    Route::apiResource('mitra-payroll', \App\Http\Controllers\MitraPayrollController::class);

    // Donatur API
    Route::get('donatur-next-kode', [DonaturController::class, 'getNextKode'])->name('admin.api.donatur.next-kode');
    Route::apiResource('donatur', DonaturController::class);
    
    // Search API
    Route::get('/search', [SearchController::class, 'search'])->name('admin.api.search');
    Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('admin.api.search.autocomplete');
     Route::apiResource('tipe-donatur', TipeDonaturController::class);

    // Transaksi API
    // Custom export endpoint for program-focused CSV export
    Route::get('transaksi/export-program', [TransaksiController::class, 'exportProgram']);
    Route::apiResource('transaksi', TransaksiController::class);
    
    // Pengajuan Dana API
    Route::get('pengajuan-dana/options', [PengajuanDanaController::class, 'options']);
    Route::post('pengajuan-dana/{id}/approve', [PengajuanDanaController::class, 'approve']);
    Route::apiResource('pengajuan-dana', PengajuanDanaController::class);

    // Penyaluran API
    Route::get('penyaluran/approved-pengajuans', [\App\Http\Controllers\PenyaluranController::class, 'approvedPengajuans']);
    Route::get('penyaluran/my-credit', [\App\Http\Controllers\PenyaluranController::class, 'myCredit']);
    Route::delete('penyaluran/images/{id}', [\App\Http\Controllers\PenyaluranController::class, 'deleteImage'])->name('admin.api.penyaluran.delete-image');
    Route::apiResource('penyaluran', \App\Http\Controllers\PenyaluranController::class);

    // Landing Profile API (single resource endpoints)
    Route::get('company/landing-profile', [\App\Http\Controllers\LandingProfileController::class, 'index']);
    Route::post('company/landing-profile', [\App\Http\Controllers\LandingProfileController::class, 'store']);
    Route::put('company/landing-profile', [\App\Http\Controllers\LandingProfileController::class, 'update']);

    // Absensi API - Custom routes MUST be defined BEFORE apiResource
    Route::get('absensi/today-status', [AbsensiController::class, 'todayStatus'])->name('admin.api.absensi.today-status');
    Route::post('absensi/clock-in', [AbsensiController::class, 'clockIn'])->name('admin.api.absensi.clock-in');
    Route::post('absensi/clock-out', [AbsensiController::class, 'clockOut'])->name('admin.api.absensi.clock-out');
    Route::get('absensi/report', [AbsensiController::class, 'report'])->name('admin.api.absensi.report');
    Route::apiResource('absensi', AbsensiController::class);

    // Laporan Keuangan API
    Route::get('laporan/keuangan', [\App\Http\Controllers\LaporanKeuanganController::class, 'index'])->name('admin.api.laporan.keuangan');
    Route::get('laporan/keuangan/program-breakdown', [\App\Http\Controllers\LaporanKeuanganController::class, 'programBreakdown'])->name('admin.api.laporan.keuangan.program-breakdown');
    Route::get('laporan/keuangan/timeline', [\App\Http\Controllers\LaporanKeuanganController::class, 'timeline'])->name('admin.api.laporan.keuangan.timeline');
    Route::get('laporan/keuangan/penyaluran-by-alias', [\App\Http\Controllers\LaporanKeuanganController::class, 'penyaluranByAlias'])->name('admin.api.laporan.keuangan.penyaluran-by-alias');
    Route::get('laporan/keuangan/expense-type-breakdown', [\App\Http\Controllers\LaporanKeuanganController::class, 'expenseTypeBreakdown'])->name('admin.api.laporan.keuangan.expense-type-breakdown');
    Route::get('laporan/keuangan/allocation-summary', [\App\Http\Controllers\LaporanKeuanganController::class, 'allocationSummary'])->name('admin.api.laporan.keuangan.allocation-summary');
    Route::get('laporan/mitra', [\App\Http\Controllers\LaporanKeuanganController::class, 'mitraList'])->name('admin.api.laporan.mitra');
    Route::get('laporan/mitra/{id}', [\App\Http\Controllers\LaporanKeuanganController::class, 'mitraDetail'])->name('admin.api.laporan.mitra.detail');
    Route::get('laporan/mitra/{id}/transactions', [\App\Http\Controllers\LaporanKeuanganController::class, 'mitraTransactions'])->name('admin.api.laporan.mitra.transactions');
    // Alias Bahasa Indonesia untuk kompatibilitas frontend lama
    Route::get('laporan/mitra/{id}/transaksi', [\App\Http\Controllers\LaporanKeuanganController::class, 'mitraTransactions'])->name('admin.api.laporan.mitra.transaksi');

    // Bank Account API
    Route::apiResource('bank-accounts', \App\Http\Controllers\BankAccountController::class);
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
Route::middleware(['auth'])->group(function () {
    // Handle /admin and /admin/ - show Vue app (Vue router will handle navigation)
    Route::get('/admin', function () {
        return view('admin.index');
    })->name('admin.index');
    
    // Specific admin routes that are server-rendered (printable views) should be declared BEFORE the catch-all Vue route
    Route::get('/admin/operasional/payroll/periods/{periodId}/records/{recordId}/slip', [\App\Http\Controllers\PayrollSlipController::class, 'slip'])->name('admin.operasional.payroll.slip');

    // The {any} parameter will match everything except signin/signup and api/*
    Route::get('/admin/{any}', function () {
        return view('admin.index');
    })->where('any', '^(?!signin$|signup$|api($|/.*)).+$')->name('admin');
});

