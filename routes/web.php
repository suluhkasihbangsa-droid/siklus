<?php

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SasaranController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\AturanInterpretasiController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\PublicRegistrationController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';

// ==================== UTILITY ROUTES ====================
// Route::get('/storage', function () {
//     Artisan::call('storage:link');
// });

// Route untuk menampilkan form pendaftaran mandiri
Route::get('/daftar-mandiri', [PublicRegistrationController::class, 'create'])->name('public.register.create');
// Route untuk memproses form dan menampilkan QR code
Route::post('/daftar-mandiri/generate-qr', [PublicRegistrationController::class, 'generateQr'])->name('public.register.generateQr');

// ==================== PUBLIC ROUTES ====================
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
})->name('home');

// Route untuk uisheet dipindah ke route yang lebih spesifik
Route::get('/uisheet', [HomeController::class, 'uisheet'])->name('uisheet');
// Route::get('/', [HomeController::class, 'uisheet'])->name('uisheet');

Route::prefix('ajax')->group(function () {
    Route::get('/get-kota/{provinsi_id}', [SasaranController::class, 'getKota'])->name('alamat.getKota');
    Route::get('/get-kecamatan/{kota_id}', [SasaranController::class, 'getKecamatan'])->name('alamat.getKecamatan');
    Route::get('/get-kelurahan/{kecamatan_id}', [SasaranController::class, 'getKelurahan'])->name('alamat.getKelurahan');
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware(['auth'])->group(function () {
    
    // -------------------- DASHBOARD --------------------
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    // -------------------- SECURITY MODULE --------------------
    Route::prefix('security')->group(function () {
        Route::get('/role-permission', [RolePermission::class, 'index'])->name('role.permission.list');
        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);
    });
    
    // -------------------- USERS MODULE --------------------
    Route::middleware(['role:admin'])->group(function() {
        Route::get('/users/download-template', [UserController::class, 'downloadTemplate'])->name('users.download_template');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    });
    
    Route::resource('users', UserController::class);

    // -------------------- ORGANISASI MODULE --------------------
    Route::resource('organisasi', OrganisasiController::class);
    Route::post('/organisasi/quick-store', [OrganisasiController::class, 'quickStore'])->name('organisasi.quickStore');    
    
    // -------------------- SASARAN MODULE --------------------
    Route::prefix('sasaran')->group(function () {
        Route::get('/', [SasaranController::class, 'index'])->name('sasaran.index');
        Route::get('/create', [SasaranController::class, 'create'])->name('sasaran.create');
        Route::post('/', [SasaranController::class, 'store'])->name('sasaran.store');
        Route::delete('/{sasaran}', [SasaranController::class, 'destroy'])->name('sasaran.destroy');
        Route::get('/{sasaran}/edit', [SasaranController::class, 'edit'])->name('sasaran.edit');
        Route::put('/{sasaran}', [SasaranController::class, 'update'])->name('sasaran.update');
        Route::get('/{sasaran}/cetak-id', [SasaranController::class, 'cetakId'])->name('sasaran.cetakId');

        Route::get('/create-from-qr', [SasaranController::class, 'createFromQr'])->name('sasaran.createFromQr');
    });

    // -------------------- PEMERIKSAAN MODULE --------------------
    Route::prefix('pemeriksaan')->group(function () {
        Route::get('/', [PemeriksaanController::class, 'index'])->name('pemeriksaan.index');
        Route::get('/create/{sasaran}', [PemeriksaanController::class, 'create'])->name('pemeriksaan.create');
        Route::post('/', [PemeriksaanController::class, 'store'])->name('pemeriksaan.store');
        Route::post('/filter', [PemeriksaanController::class, 'filter'])->name('pemeriksaan.filter');
        Route::get('/{pemeriksaan}/edit', [PemeriksaanController::class, 'edit'])->name('pemeriksaan.edit');
        
        // UBAH Route::put menjadi Route::post untuk update agar kompatibel dengan form HTML
        Route::post('/{pemeriksaan}', [PemeriksaanController::class, 'update'])->name('pemeriksaan.update'); 
        
        Route::delete('/{pemeriksaan}', [PemeriksaanController::class, 'destroy'])->name('pemeriksaan.destroy');
        Route::get('/export', [PemeriksaanController::class, 'exportExcel'])->name('pemeriksaan.export');
    });

    // --- KONSULTASI ---
    Route::prefix('konsultasi')->middleware(['auth', 'role:admin|dokter'])->name('konsultasi.')->group(function () {
        Route::get('/', [KonsultasiController::class, 'index'])->name('index');
        Route::get('/pemeriksaan/{pemeriksaan}/create', [KonsultasiController::class, 'create'])->name('create');
        Route::post('/', [KonsultasiController::class, 'store'])->name('store');
    });

    // Route KHUSUS UNTUK CETAK, bisa diakses oleh lebih banyak role
    Route::get('/konsultasi/pemeriksaan/{pemeriksaan}/cetak', [KonsultasiController::class, 'cetak'])
        ->middleware(['auth', 'role:admin|dokter|user|koorUser']) // <-- Izin akses diperluas di sini
        ->name('konsultasi.cetak');        

    // -------------------- PENGATURAN SISTEM (ADMIN ONLY) --------------------
    Route::middleware(['role:admin'])->group(function() {
        // Route::resource('users', UserController::class);
        Route::resource('aturan-interpretasi', AturanInterpretasiController::class); // <-- FIX: Tambahkan route ini
        
        // Security Module
        Route::prefix('security')->group(function () {
            Route::get('/role-permission', [RolePermission::class, 'index'])->name('role.permission.list');
            Route::resource('permission', PermissionController::class);
            Route::resource('role', RoleController::class);
        });
    });    
    
    // -------------------- AJAX DATA ROUTES --------------------
    Route::prefix('ajax')->group(function () {
        // Organisasi routes
        Route::get('/get-sub-organisasi/{parent_id}', [SasaranController::class, 'getSubOrganisasi'])->name('organisasi.getSub');
        Route::post('/get-interpretasi', [PemeriksaanController::class, 'ajaxGetInterpretasi'])
        ->name('ajax.getInterpretasi');

        Route::get('/cari-obat', [KonsultasiController::class, 'cariObat'])->name('ajax.cariObat');
        Route::get('/get-hasil-konsultasi/{pemeriksaan}', [KonsultasiController::class, 'getHasilKonsultasi'])->name('ajax.getHasilKonsultasi');

    });
});

// ==================== UI COMPONENTS & PAGES ROUTES TEMPLATE====================

// -------------------- MENU STYLE PAGES --------------------
Route::prefix('menu-style')->group(function() {
    Route::get('horizontal', [HomeController::class, 'horizontal'])->name('menu-style.horizontal');
    Route::get('dual-horizontal', [HomeController::class, 'dualhorizontal'])->name('menu-style.dualhorizontal');
    Route::get('dual-compact', [HomeController::class, 'dualcompact'])->name('menu-style.dualcompact');
    Route::get('boxed', [HomeController::class, 'boxed'])->name('menu-style.boxed');
    Route::get('boxed-fancy', [HomeController::class, 'boxedfancy'])->name('menu-style.boxedfancy');
});

// -------------------- SPECIAL PAGES --------------------
Route::prefix('special-pages')->group(function() {
    Route::get('billing', [HomeController::class, 'billing'])->name('special-pages.billing');
    Route::get('calender', [HomeController::class, 'calender'])->name('special-pages.calender');
    Route::get('kanban', [HomeController::class, 'kanban'])->name('special-pages.kanban');
    Route::get('pricing', [HomeController::class, 'pricing'])->name('special-pages.pricing');
    Route::get('rtl-support', [HomeController::class, 'rtlsupport'])->name('special-pages.rtlsupport');
    Route::get('timeline', [HomeController::class, 'timeline'])->name('special-pages.timeline');
});

// -------------------- WIDGET PAGES --------------------
Route::prefix('widget')->group(function() {
    Route::get('widget-basic', [HomeController::class, 'widgetbasic'])->name('widget.widgetbasic');
    Route::get('widget-chart', [HomeController::class, 'widgetchart'])->name('widget.widgetchart');
    Route::get('widget-card', [HomeController::class, 'widgetcard'])->name('widget.widgetcard');
});

// -------------------- MAPS PAGES --------------------
Route::prefix('maps')->group(function() {
    Route::get('google', [HomeController::class, 'google'])->name('maps.google');
    Route::get('vector', [HomeController::class, 'vector'])->name('maps.vector');
});

// -------------------- AUTH PAGES --------------------
Route::prefix('auth')->group(function() {
    Route::get('signin', [HomeController::class, 'signin'])->name('auth.signin');
    Route::get('signup', [HomeController::class, 'signup'])->name('auth.signup');
    Route::get('confirmmail', [HomeController::class, 'confirmmail'])->name('auth.confirmmail');
    Route::get('lockscreen', [HomeController::class, 'lockscreen'])->name('auth.lockscreen');
    Route::get('recoverpw', [HomeController::class, 'recoverpw'])->name('auth.recoverpw');
    Route::get('userprivacysetting', [HomeController::class, 'userprivacysetting'])->name('auth.userprivacysetting');
});

// -------------------- ERROR PAGES --------------------
Route::prefix('errors')->group(function() {
    Route::get('error404', [HomeController::class, 'error404'])->name('errors.error404');
    Route::get('error500', [HomeController::class, 'error500'])->name('errors.error500');
    Route::get('maintenance', [HomeController::class, 'maintenance'])->name('errors.maintenance');
});

// -------------------- FORM PAGES --------------------
Route::prefix('forms')->group(function() {
    Route::get('element', [HomeController::class, 'element'])->name('forms.element');
    Route::get('wizard', [HomeController::class, 'wizard'])->name('forms.wizard');
    Route::get('validation', [HomeController::class, 'validation'])->name('forms.validation');
});

// -------------------- TABLE PAGES --------------------
Route::prefix('table')->group(function() {
    Route::get('bootstraptable', [HomeController::class, 'bootstraptable'])->name('table.bootstraptable');
    Route::get('datatable', [HomeController::class, 'datatable'])->name('table.datatable');
});

// -------------------- ICON PAGES --------------------
Route::prefix('icons')->group(function() {
    Route::get('solid', [HomeController::class, 'solid'])->name('icons.solid');
    Route::get('outline', [HomeController::class, 'outline'])->name('icons.outline');
    Route::get('dualtone', [HomeController::class, 'dualtone'])->name('icons.dualtone');
    Route::get('colored', [HomeController::class, 'colored'])->name('icons.colored');
});

// -------------------- EXTRA PAGES --------------------
Route::get('privacy-policy', [HomeController::class, 'privacypolicy'])->name('pages.privacy-policy');
Route::get('terms-of-use', [HomeController::class, 'termsofuse'])->name('pages.term-of-use');