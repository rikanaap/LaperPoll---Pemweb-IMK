<?php

use App\Models\Resep;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PencarianResepController;
use App\Http\Controllers\SwipeResepController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/', function () {
//     return view('user.landing');
// });

Route::get('/detail-resep', function () {
    return view('pages.detail_resep.detail_resep');
});

Route::get('/main-menu', function () {
    $reseps = Resep::with('user')->get();
    return view('pages.main-menu.main-menu', compact('reseps'));
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

Route::get('/profile', [ProfileController::class, 'index']);