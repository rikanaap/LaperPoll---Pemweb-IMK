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

// Harmoni -> Link Landing Page
Route::get('/', [LandingPage::class, 'index'])
    ->name('landing.index');

// Harmoni -> Link Authentication
Route::get('/auth', function () {
    return view('pages.auth.auth');
});

// Harmoni -> Link Main Menu
Route::get('/main-menu', [MainMenu::class, 'index'])
    ->name('main-menu.index');
Route::get('/main-menu?m=favorit', [MainMenu::class, 'favoritPengguna'])
    ->name('main-menu.favorit');
Route::get('/main-menu?m=hari', [MainMenu::class, 'resepHariIni'])
    ->name('main-menu.hari-ini');



Route::get('/detail-resep', function () {
    return view('pages.detail_resep.detail_resep');
});

Route::get('/timer-resep', function () {
    return view('pages.timer_resep.timer_resep');
});

Route::get('/ulasan', function () {
    return view('pages.ulasan.ulasan');
});

Route::middleware(['auth'])->group(function () {
    
    Route::post('/favorit/toggle/{id}', [FavoriteController::class, 'toggle'])->name('favorit.toggle');
});

// Ikbal -> link halaman pencarian resep
Route::get('/pencarian-resep', [PencarianResepController::class, 'index'])
    ->name('pencarian.resep');
Route::get('/filter-resep', [PencarianResepController::class, 'filter'])
    ->name('filter.resep');

// Ikbal -> link untuk akses swipe rasa
Route::get('/swipe-rasa', [SwipeResepController::class, 'index'])
    ->name('swipe.rasa');
Route::get('/filter-resep-swipe', [SwipeResepController::class, 'showFilter'])
    ->name('filter.index');

// ─── KULKAS DIGITAL (Ansori) ────────────────────────────────────────────────
// Halaman utama kulkas
Route::get('/kulkas-digital', [KulkasDigitalController::class, 'index'])
    ->name('kulkas.index');

// Form tambah bahan ke kulkas → view: pages/tambah/bahan/tambah.blade.php
Route::get('/kulkas-digital/tambah', [KulkasDigitalController::class, 'tambah'])
    ->name('kulkas.tambah');

// Simpan bahan ke kulkas
Route::post('/kulkas-digital', [KulkasDigitalController::class, 'store'])
    ->name('kulkas.store');

// Hapus item dari kulkas
Route::delete('/kulkas-digital/{id}', [KulkasDigitalController::class, 'destroy'])
    ->name('kulkas.destroy');

// ─── MASTER DATA BAHAN (Admin) ──────────────────────────────────────────────
// CATATAN: BahansController BUKAN untuk "tambah bahan ke kulkas".
// Ini untuk mengelola daftar master bahan yang tersedia di sistem.
// Tambahkan middleware admin sesuai kebutuhan project.
Route::prefix('admin/bahans')->name('admin.bahans.')->group(function () {
    Route::get('/',           [BahansController::class, 'index'])->name('index');
    Route::get('/tambah',     [BahansController::class, 'create'])->name('create');
    Route::post('/',          [BahansController::class, 'store'])->name('store');
    Route::get('/{bahan}/edit', [BahansController::class, 'edit'])->name('edit');
    Route::put('/{bahan}',    [BahansController::class, 'update'])->name('update');
    Route::delete('/{bahan}', [BahansController::class, 'destroy'])->name('destroy');
});

// API endpoint — daftar bahan untuk autocomplete JS
Route::get('/api/bahans', [BahansController::class, 'apiList'])
    ->name('api.bahans');

// ─── MEAL PLANNER ───────────────────────────────────────────────────────────
Route::get('/meal-planner', [MealPlannerController::class, 'index'])
    ->name('meal-planner.index');

// ─── PILIH RESEP ────────────────────────────────────────────────────────────
Route::get('/pilih-resep', [PilihResepController::class, 'index'])
    ->name('pilih-resep.index');

// ─── NOTA BELANJA ───────────────────────────────────────────────────────────
Route::get('/nota-belanja', [NotaBelAnjaController::class, 'index'])
    ->name('nota.index');

// ─── PROFILE ────────────────────────────────────────────────────────────────
Route::get('/profile', function () {
    return view('pages.profile.index');
})->name('profile.index');
