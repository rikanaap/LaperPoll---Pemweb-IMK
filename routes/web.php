<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PencarianResepController;
use App\Http\Controllers\SwipeResepController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KulkasDigitalController;
use App\Http\Controllers\BahansController;          // Controller master data bahan (admin)
use App\Http\Controllers\MealPlannerController;
use App\Http\Controllers\PilihResepController;
use App\Http\Controllers\NotaBelAnjaController;
use App\Http\Controllers\LandingPage;
use App\Http\Controllers\MainMenu;

use App\Http\Controllers\Api\ResepApiController;
use App\Http\Controllers\Api\SwipeResepApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DetailResepController;
use App\Http\Controllers\FavoriteController;

// Harmoni -> Link Landing Page
Route::get('/', [LandingPage::class, 'index'])
    ->name('landing.index');

// Route halaman unauthorized
Route::get('/unauthorized', function () {
    return view('pages.unauthorized.index');
})->name('unauthorized');

// Auth Pages
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/sign-in', [AuthController::class, 'signIn'])
        ->name('sign-in');

    Route::get('/sign-up', [AuthController::class, 'signUp'])
        ->name('sign-up');

    Route::get('/forgot-pass', [AuthController::class, 'forgotPass'])
        ->name('forgot-pass');

    //API
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Harmoni -> Link Main Menu
Route::get('/main-menu', [MainMenu::class, 'index'])
    ->name('main-menu.index');
    
Route::get('/detail-resep/{id}', [DetailResepController::class, 'showwDetail'])
    ->name('detail_resep.index');

Route::get('/timer-resep', function () {
    return view('pages.timer_resep.timer_resep');
});
Route::get('/ulasan', function () {
    return view('pages.ulasan.ulasan');
});

Route::get('/favorit', function () {
    return view('pages.favorit.index'); 
});
// Ikbal -> link halaman pencarian resep
Route::get('/pencarian-resep', [PencarianResepController::class, 'index'])
    ->name('pencarian.resep');
Route::get('/filter-resep', [PencarianResepController::class, 'filter'])
    ->name('filter.resep');


Route::prefix('api')->group(function () {
    Route::get('/resep/search', [ResepApiController::class, 'search'])
        ->name('api.resep.search');
    Route::get('/bahan/by-ids', [ResepApiController::class, 'getBahansByIds'])
        ->name('api.bahan.by-ids');
    Route::get('/swipe-rasa', [SwipeResepApiController::class, 'getRasa']);
    Route::post('/filter-resep-swipe', [SwipeResepApiController::class, 'filterResep']);
});

// Ikbal -> link untuk akses swipe rasa
Route::get('/swipe-rasa', [SwipeResepController::class, 'index'])
    ->name('swipe.rasa');
Route::get('/filter-resep-swipe', [SwipeResepController::class, 'showFilter'])
    ->name('filter.index');


// ─── Semua yang butuh login ──────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::post('/favorit/toggle/{id}', [FavoriteController::class, 'toggle'])
        ->name('favorit.toggle');

    // ─── KULKAS DIGITAL ──────────────────────────────────────────────────────
    Route::get('/kulkas-digital', [KulkasDigitalController::class, 'index'])
        ->name('kulkas.index');
    Route::get('/kulkas-digital/tambah', [KulkasDigitalController::class, 'tambah'])
        ->name('kulkas.tambah');
    Route::post('/kulkas-digital', [KulkasDigitalController::class, 'store'])
        ->name('kulkas.store');
    // ⚠️ pakai-resep harus di atas /{id} supaya tidak bentrok dengan wildcard
    Route::post('/kulkas-digital/pakai-resep', [KulkasDigitalController::class, 'pakaiResep'])
        ->name('kulkas.pakai-resep');
    Route::delete('/kulkas-digital/{id}', [KulkasDigitalController::class, 'destroy'])
        ->name('kulkas.destroy');

    // ─── MEAL PLANNER ────────────────────────────────────────────────────────
    Route::get('/meal-planner', [MealPlannerController::class, 'index'])
        ->name('meal-planner.index');
    Route::get('/api/meal-planner', [MealPlannerController::class, 'getData'])
        ->name('api.meal-planner.data');
    Route::post('/api/meal-planner/kalori', [MealPlannerController::class, 'setKalori'])
        ->name('api.meal-planner.kalori');
    Route::post('/api/meal-planner/tambah', [MealPlannerController::class, 'tambahResep'])
        ->name('api.meal-planner.tambah');
    Route::delete('/api/meal-planner/detail/{id}', [MealPlannerController::class, 'hapusDetail'])
        ->name('api.meal-planner.hapus');
    Route::post('/api/meal-planner/generate-nota', [MealPlannerController::class, 'generateNota'])
        ->name('api.meal-planner.generate-nota');

    // ─── PILIH RESEP ─────────────────────────────────────────────────────────
    Route::get('/pilih-resep', [PilihResepController::class, 'index'])
        ->name('pilih-resep.index');

    // ─── NOTA BELANJA ────────────────────────────────────────────────────────
    Route::get('/nota-belanja', [NotaBelAnjaController::class, 'index'])
        ->name('nota.index');

    // ─── PROFILE ─────────────────────────────────────────────────────────────
    Route::get('/profile', function () {
        return view('pages.profile.index');
    })->name('profile.index');

});


// ─── MASTER DATA BAHAN (Admin) ──────────────────────────────────────────────
Route::prefix('admin/bahans')->name('admin.bahans.')->group(function () {
    Route::get('/',             [BahansController::class, 'index'])->name('index');
    Route::get('/tambah',       [BahansController::class, 'create'])->name('create');
    Route::post('/',            [BahansController::class, 'store'])->name('store');
    Route::get('/{bahan}/edit', [BahansController::class, 'edit'])->name('edit');
    Route::put('/{bahan}',      [BahansController::class, 'update'])->name('update');
    Route::delete('/{bahan}',   [BahansController::class, 'destroy'])->name('destroy');
});

Route::get('/api/bahans', [BahansController::class, 'apiList'])->name('api.bahans');

// AJAX: simpan bahan baru manual yang belum ada di DB (public, tidak butuh auth)
Route::post('/api/bahans/baru', [KulkasDigitalController::class, 'storeBahanBaru'])
    ->name('kulkas.bahan.baru');