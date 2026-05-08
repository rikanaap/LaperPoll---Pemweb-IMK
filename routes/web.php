<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PencarianResepController;
use App\Http\Controllers\SwipeResepController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KulkasDigitalController;
use App\Http\Controllers\LandingPage;
use App\Http\Controllers\MainMenu;
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

// Ansori -> Kulkas Digital 
Route::get('/kulkas-digital', [KulkasDigitalController::class, 'index'])
    ->name('kulkas.index');

Route::get('/kulkas-digital/tambah',  [KulkasDigitalController::class, 'tambah'])
    ->name('kulkas.tambah');

Route::post('/kulkas-digital', [KulkasDigitalController::class, 'store'])
    ->name('kulkas.store');

Route::delete('/kulkas-digital/{id}', [KulkasDigitalController::class, 'destroy'])
    ->name('kulkas.destroy');

// Meal Planner
Route::get('/meal-planner', [MealPlannerController::class, 'index'])
    ->name('meal-planner.index');

// Pilih Resep
Route::get('/pilih-resep', [PilihResepController::class, 'index'])
    ->name('pilih-resep.index');

// Nota Belanja
Route::get('/nota-belanja', [NotaBelAnjaController::class, 'index'])
    ->name('nota.index');

// Profile placeholder
Route::get('/profile', function () {
    return view('pages.profile.index');
})->name('profile.index');
