<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PencarianResepController;
use App\Http\Controllers\SwipeResepController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\FollowController;
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
use App\Http\Controllers\DetailResepController;
use App\Http\Controllers\TimerResepController;
use App\Http\Controllers\UlasanController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminResepController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminBahanController;
use App\Http\Controllers\Admin\AdminFilterController;


// ─── PUBLIC ──────────────────────────────────────────────────────────────────

Route::get('/', [LandingPage::class, 'index'])->name('landing.index');

Route::get('/unauthorized', fn() => view('pages.unauthorized.index'))->name('unauthorized');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/sign-in',     [AuthController::class, 'signIn'])    ->name('sign-in');
    Route::get('/sign-up',     [AuthController::class, 'signUp'])    ->name('sign-up');
    Route::get('/forgot-pass', [AuthController::class, 'forgotPass'])->name('forgot-pass');
    Route::post('/register',   [AuthController::class, 'register'])  ->name('register');
    Route::post('/login',      [AuthController::class, 'login'])     ->name('login');
    Route::post('/logout',     [AuthController::class, 'logout'])    ->name('logout');
});

Route::get('/main-menu',          [MainMenu::class, 'index'])          ->name('main-menu.index');
Route::get('/main-menu?m=favorit',[MainMenu::class, 'favoritPengguna'])->name('main-menu.favorit');
Route::get('/main-menu?m=hari',   [MainMenu::class, 'resepHariIni'])  ->name('main-menu.hari-ini');

// ── DETAIL RESEP ──────────────────────────────────────────────────────────────
Route::get('/detail-resep/{id}', [DetailResepController::class, 'showDetail'])->name('detail.resep');

// ── TIMER RESEP ───────────────────────────────────────────────────────────────
Route::get('/timer-resep/{id}', [TimerResepController::class, 'show'])->name('timer.resep');

// ── ULASAN ────────────────────────────────────────────────────────────────────
Route::get('/ulasan/{id}',   [UlasanController::class, 'show'])  ->name('ulasan.show');
Route::post('/ulasan/{id}',  [UlasanController::class, 'store']) ->name('ulasan.store');
Route::get('/ulasan/{resepId}/edit/{feedbackId}',    [UlasanController::class, 'edit'])   ->name('ulasan.edit');
Route::patch('/ulasan/{resepId}/update/{feedbackId}',[UlasanController::class, 'update']) ->name('ulasan.update');
Route::delete('/ulasan/{resepId}/delete/{feedbackId}',[UlasanController::class, 'destroy'])->name('ulasan.destroy');

// ── PENCARIAN RESEP ───────────────────────────────────────────────────────────
Route::get('/pencarian-resep', [PencarianResepController::class, 'index'])
    ->name('pencarian.resep');

// ── SWIPE RASA ────────────────────────────────────────────────────────────────
Route::get('/swipe-rasa', [SwipeResepController::class, 'index'])
    ->name('swipe.rasa');

Route::get('/filter-resep-swipe', [SwipeResepController::class, 'showFilter'])
    ->name('swipe.filter');


// ─── API PUBLIK ───────────────────────────────────────────────────────────────

Route::prefix('api')->name('api.')->group(function () {

    // Resep
    Route::prefix('resep')->name('resep.')->group(function () {
        Route::get('search',        [ResepApiController::class, 'search'])     ->name('search');
        Route::post('render-cards', [ResepApiController::class, 'renderCards'])->name('render-cards');
    });

    // Bahan
    Route::prefix('bahan')->name('bahan.')->group(function () {
        Route::get('by-ids', [ResepApiController::class, 'getBahansByIds'])->name('by-ids');
    });

    Route::get('/bahans', [BahansController::class, 'apiList'])->name('bahans');

    // Swipe
    Route::prefix('swipe')->name('swipe.')->group(function () {
        Route::get('/rasa',              [SwipeResepApiController::class, 'getRasa'])      ->name('rasa');
        Route::get('/filter-resep-swipe',[SwipeResepApiController::class, 'filterSwipe']) ->name('filter.resep.swipe');
    });
});


// ─── AUTH REQUIRED ────────────────────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {

    Route::post('/favorit/toggle/{id}', [FavoriteController::class, 'toggle'])->name('favorit.toggle');
    Route::get('/favorit',              [FavoriteController::class, 'index']) ->name('favorit.index');

    // ── KULKAS DIGITAL ────────────────────────────────────────────────────────
    Route::get('/kulkas-digital',        [KulkasDigitalController::class, 'index'])    ->name('kulkas.index');
    Route::get('/kulkas-digital/tambah', [KulkasDigitalController::class, 'tambah'])  ->name('kulkas.tambah');
    Route::post('/kulkas-digital',       [KulkasDigitalController::class, 'store'])   ->name('kulkas.store');
    // ⚠️ pakai-resep HARUS di atas /{id}
    Route::post('/kulkas-digital/pakai-resep', [KulkasDigitalController::class, 'pakaiResep'])->name('kulkas.pakai-resep');
    Route::delete('/kulkas-digital/{id}',      [KulkasDigitalController::class, 'destroy'])   ->name('kulkas.destroy');
    Route::post('/api/bahans/baru', [KulkasDigitalController::class, 'storeBahanBaru'])->name('kulkas.bahan.baru');

    // ── MEAL PLANNER ──────────────────────────────────────────────────────────
    Route::get('/meal-planner', [MealPlannerController::class, 'index'])->name('meal-planner.index');

    Route::prefix('api/meal-planner')->group(function () {
        Route::get('/',               [MealPlannerController::class, 'getData'])     ->name('api.meal-planner.data');
        Route::post('/kalori',        [MealPlannerController::class, 'setKalori'])  ->name('api.meal-planner.kalori');
        Route::post('/tambah',        [MealPlannerController::class, 'tambahResep'])->name('api.meal-planner.tambah');
        Route::delete('/detail/{id}', [MealPlannerController::class, 'hapusDetail'])->name('api.meal-planner.hapus');
        Route::post('/generate-nota', [MealPlannerController::class, 'generateNota'])->name('api.meal-planner.generate-nota');
    });

    // ── PILIH RESEP ───────────────────────────────────────────────────────────
    Route::get('/pilih-resep', [PilihResepController::class, 'index'])->name('pilih-resep.index');

    // ── NOTA BELANJA ──────────────────────────────────────────────────────────
    Route::get('/nota-belanja', [NotaBelanjaController::class, 'index'])->name('nota.index');

    Route::prefix('api/nota-belanja')->group(function () {
        Route::patch('/toggle/{id}',    [NotaBelanjaController::class, 'toggle'])      ->name('api.nota.toggle');
        Route::delete('/hapus-selesai', [NotaBelanjaController::class, 'hapusSelesai'])->name('api.nota.hapus-selesai');
        Route::delete('/{id}',          [NotaBelanjaController::class, 'destroy'])     ->name('api.nota.destroy');
    });

    // ── PROFILE ───────────────────────────────────────────────────────────────
    Route::get('/profile',          [ProfileController::class, 'index']) ->name('profile.index');
    Route::get('/profile/edit',     [ProfileController::class, 'edit'])  ->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

// ── PROFIL PUBLIK (bisa diakses guest) ───────────────────────────────────────
Route::get('/profile/{id}', [PublicProfileController::class, 'show'])->name('profile.public');

// ── FOLLOW (auth required) ────────────────────────────────────────────────────
Route::get('/follow/{userId}/followers', [FollowController::class, 'followers'])->name('follow.followers');
Route::get('/follow/{userId}/following', [FollowController::class, 'following'])->name('follow.following');
Route::post('/follow/{userId}/toggle',   [FollowController::class, 'toggle'])   ->name('follow.toggle');

});


// ─── ADMIN ────────────────────────────────────────────────────────────────────

Route::prefix('admin/bahans')->name('admin.bahans.')->group(function () {
    Route::get('/',             [BahansController::class, 'index'])  ->name('index');
    Route::get('/tambah',       [BahansController::class, 'create']) ->name('create');
    Route::post('/',            [BahansController::class, 'store'])  ->name('store');
    Route::get('/{bahan}/edit', [BahansController::class, 'edit'])   ->name('edit');
    Route::put('/{bahan}',      [BahansController::class, 'update']) ->name('update');
    Route::delete('/{bahan}',   [BahansController::class, 'destroy'])->name('destroy');
});

 
Route::prefix('admin')->name('admin.')->group(function () {
 
    Route::get('/',        [AdminDashboardController::class, 'index'])->name('dashboard');
 
    Route::prefix('resep')->name('resep.')->group(function () {
        Route::get('/',                    [AdminResepController::class, 'index'])->name('index');
        Route::get('/{resep}',             [AdminResepController::class, 'show'])->name('show');
        Route::patch('/{resep}/publish',   [AdminResepController::class, 'togglePublish'])->name('togglePublish');
        Route::delete('/{resep}',          [AdminResepController::class, 'destroy'])->name('destroy');
    });
 
        Route::prefix('user')->name('user.')->group(function () {
        Route::get('/',                [AdminUserController::class, 'index'])->name('index');
        Route::patch('/{user}',        [AdminUserController::class, 'update'])->name('update');
        Route::patch('/{user}/verify', [AdminUserController::class, 'verify'])->name('verify');
        Route::delete('/{user}',       [AdminUserController::class, 'destroy'])->name('destroy');
    });

        Route::prefix('bahan')->name('bahan.')->group(function () {
        Route::get('/',          [AdminBahanController::class, 'index'])->name('index');
        Route::post('/',         [AdminBahanController::class, 'store'])->name('store');
        Route::patch('/{bahan}', [AdminBahanController::class, 'update'])->name('update');
        Route::delete('/{bahan}',[AdminBahanController::class, 'destroy'])->name('destroy');
    });
    

        Route::prefix('filter')->name('filter.')->group(function () {
        Route::get('/',            [AdminFilterController::class, 'index'])->name('index');
        Route::post('/',           [AdminFilterController::class, 'store'])->name('store');
        Route::patch('/{filter}',  [AdminFilterController::class, 'update'])->name('update');
        Route::delete('/{filter}', [AdminFilterController::class, 'destroy'])->name('destroy');
    });
});