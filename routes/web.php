<?php

use App\Models\Resep;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BahanController;
use App\Http\Controllers\SwipeResepController;
use App\Http\Controllers\KulkasDigitalController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/', function () {
//     return view('user.landing');
// });

Route::get('/kulkas-digital', [KulkasDigitalController::class, 'index'])
    ->name('kulkas.index');

Route::get('/detail-resep', function () {
    return view('pages.detail_resep.detail_resep');
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