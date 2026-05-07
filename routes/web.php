<?php

use App\Models\Resep;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BahanController;
use App\Http\Controllers\SwipeResepController;
use App\Http\Controllers\KulkasDigitalController;
use App\Http\Controllers\MealPlannerController;
use App\Http\Controllers\PilihResepController;
use App\Http\Controllers\NotaBelAnjaController;

Route::get('/', function () {
    return view('index');
});

Route::get('/detail-resep', function () {
    return view('pages.detail_resep.detail_resep');
});

Route::get('/timer-resep', function () {
    return view('pages.timer_resep.timer_resep');
});

Route::get('/ulasan', function () {
    return view('pages.ulasan.ulasan');
});

Route::get('/main-menu', function () {
    $reseps = Resep::with('user')->get();
    return view('pages.main-menu.main-menu', compact('reseps'));
});

// Ikbal -> link halaman pencarian resep
Route::get('/pencarian-resep', [BahanController::class, 'index'])
    ->name('pencarian.resep');

// Ikbal -> link untuk akses swipe rasa
Route::get('/swipe-rasa', [SwipeResepController::class, 'index'])
    ->name('swipe.rasa');

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