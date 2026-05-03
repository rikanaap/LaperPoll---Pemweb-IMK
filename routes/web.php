<?php

use App\Models\Resep;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BahanController;
use App\Http\Controllers\SwipeResepController;

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


//ikbal -> link halaman pencarian resep
Route::view('/pencarian-resep', 'pages.pencarian-resep.index')
    ->name('pencarian.resep');

Route::get('/pencarian-resep', [BahanController::class, 'index'])->name('pencarian.resep');


//Ikbal - link untuk akses swipe rasa 
Route::view('/swipe-rasa', 'pages.swipe_resep.index')
    ->name('swipe.rasa');

Route::get('/swipe-rasa', [SwipeResepController::class, 'index'])->name('swipe-resep.index');
