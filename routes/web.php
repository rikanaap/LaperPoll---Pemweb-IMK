<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PencarianResepController;
use App\Http\Controllers\SwipeResepController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KulkasDigitalController;
use App\Http\Controllers\BahansController;
use App\Http\Controllers\MealPlannerController;
use App\Http\Controllers\PilihResepController;
use App\Http\Controllers\NotaBelanjaController;
use App\Http\Controllers\LandingPage;
use App\Http\Controllers\MainMenu;
use App\Http\Controllers\Api\ResepApiController;
use App\Http\Controllers\Api\SwipeResepApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;

// ─── PUBLIC ─────────────────────────────────────────────────────────────────

Route::get('/', [LandingPage::class, 'index'])->name('landing.index');

Route::get('/unauthorized', fn() => view('pages.unauthorized.index'))->name('unauthorized');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/sign-in',     [AuthController::class, 'signIn'])    ->name('sign-in');
    Route::get('/sign-up',     [AuthController::class, 'signUp'])    ->name('sign-up');
    Route::get('/forgot-pass', [AuthController::class, 'forgotPass'])->name('forgot-pass');
    Route::post('/register',   [AuthController::class, 'register'])  ->name('auth.register');
    Route::post('/login',      [AuthController::class, 'login'])     ->name('auth.login');
    Route::post('/logout',     [AuthController::class, 'logout'])    ->name('auth.logout');
});

Route::get('/main-menu',         [MainMenu::class, 'index'])          ->name('main-menu.index');
Route::get('/main-menu?m=favorit',[MainMenu::class, 'favoritPengguna'])->name('main-menu.favorit');
Route::get('/main-menu?m=hari',  [MainMenu::class, 'resepHariIni'])  ->name('main-menu.hari-ini');

Route::get('/detail-resep', fn() => view('pages.detail_resep.detail_resep'))->name('detail.resep');
Route::get('/timer-resep',  fn() => view('pages.timer_resep.timer_resep'));
Route::get('/ulasan',       fn() => view('pages.ulasan.ulasan'));


// Ikbal -> link halaman pencarian resep
Route::get('/pencarian-resep', [PencarianResepController::class, 'index'])->name('pencarian.resep');

Route::get('/swipe-rasa', [SwipeResepController::class, 'index'])->name('swipe.rasa');
Route::get('/filter-resep-swipe', [SwipeResepController::class, 'showFilter'])->name('swipe.filter');


// API publik
// Tambahkan ->name('api.') di baris ini mas bro
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/resep/search', [ResepApiController::class, 'search'])->name('resep.search');
    Route::get('/bahan/by-ids', [ResepApiController::class, 'getBahansByIds'])->name('bahan.by-ids');
    Route::get('/bahans', [BahansController::class, 'apiList'])->name('bahans');
    Route::post('/bahans/baru', [KulkasDigitalController::class, 'storeBahanBaru'])->name('kulkas.bahan.baru');
    
    Route::prefix('swipe')->name('swipe.')->group(function () {
        Route::get('/rasa', [SwipeResepApiController::class, 'getRasa'])->name('rasa');
        Route::get('/filter-resep-swipe', [SwipeResepApiController::class, 'filterSwipe'])->name('filter.resep.swipe');
    });
});

// ─── AUTH REQUIRED ───────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::post('/favorit/toggle/{id}', [FavoriteController::class, 'toggle'])->name('favorit.toggle');

    // ── KULKAS DIGITAL ────────────────────────────────────────────────────────
    Route::get('/kulkas-digital',        [KulkasDigitalController::class, 'index']) ->name('kulkas.index');
    Route::get('/kulkas-digital/tambah', [KulkasDigitalController::class, 'tambah'])->name('kulkas.tambah');
    Route::post('/kulkas-digital',       [KulkasDigitalController::class, 'store']) ->name('kulkas.store');
    // ⚠️ pakai-resep HARUS di atas /{id}
    Route::post('/kulkas-digital/pakai-resep', [KulkasDigitalController::class, 'pakaiResep'])->name('kulkas.pakai-resep');
    Route::delete('/kulkas-digital/{id}',      [KulkasDigitalController::class, 'destroy'])   ->name('kulkas.destroy');

    // ── MEAL PLANNER ──────────────────────────────────────────────────────────
    Route::get('/meal-planner', [MealPlannerController::class, 'index'])->name('meal-planner.index');

    // API meal planner — sesuai controller kamu
    Route::prefix('api/meal-planner')->group(function () {
        Route::get('/',                  [MealPlannerController::class, 'getData'])    ->name('api.meal-planner.data');
        Route::post('/kalori',           [MealPlannerController::class, 'setKalori']) ->name('api.meal-planner.kalori');
        Route::post('/tambah',           [MealPlannerController::class, 'tambahResep'])->name('api.meal-planner.tambah');
        Route::delete('/detail/{id}',    [MealPlannerController::class, 'hapusDetail'])->name('api.meal-planner.hapus');
        Route::post('/generate-nota',    [MealPlannerController::class, 'generateNota'])->name('api.meal-planner.generate-nota');
    });

    // ── PILIH RESEP ───────────────────────────────────────────────────────────
    Route::get('/pilih-resep', [PilihResepController::class, 'index'])->name('pilih-resep.index');

    // ── NOTA BELANJA ──────────────────────────────────────────────────────────
    Route::get('/nota-belanja', [NotaBelanjaController::class, 'index'])->name('nota.index');

    Route::prefix('api/nota-belanja')->group(function () {
        Route::patch('/toggle/{id}',  [NotaBelanjaController::class, 'toggle'])      ->name('api.nota.toggle');
        Route::delete('/hapus-selesai', [NotaBelanjaController::class, 'hapusSelesai'])->name('api.nota.hapus-selesai');
        Route::delete('/{id}',        [NotaBelanjaController::class, 'destroy'])     ->name('api.nota.destroy');
    });

    // ── PROFILE ───────────────────────────────────────────────────────────────
    Route::get('/profile', fn() => view('pages.profile.index'))->name('profile.index');

});

// ─── ADMIN ───────────────────────────────────────────────────────────────────
Route::prefix('admin/bahans')->name('admin.bahans.')->group(function () {
    Route::get('/',             [BahansController::class, 'index'])  ->name('index');
    Route::get('/tambah',       [BahansController::class, 'create']) ->name('create');
    Route::post('/',            [BahansController::class, 'store'])  ->name('store');
    Route::get('/{bahan}/edit', [BahansController::class, 'edit'])   ->name('edit');
    Route::put('/{bahan}',      [BahansController::class, 'update']) ->name('update');
    Route::delete('/{bahan}',   [BahansController::class, 'destroy'])->name('destroy');
});