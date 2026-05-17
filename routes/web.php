<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingPage;
use App\Http\Controllers\MainMenu;
use App\Http\Controllers\PencarianResepController;
use App\Http\Controllers\SwipeResepController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KulkasDigitalController;
use App\Http\Controllers\BahansController;
use App\Http\Controllers\MealPlannerController;
use App\Http\Controllers\PilihResepController;
use App\Http\Controllers\NotaBelAnjaController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Api\ResepApiController;
use App\Http\Controllers\Api\SwipeResepApiController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.sign-in');

Route::get('/auth', function () {
    return view('pages.auth.auth');
})->name('auth.page');

Route::get('/', [LandingPage::class, 'index'])->name('landing.index');

Route::prefix('main-menu')->name('main-menu.')->group(function () {
    Route::get('/', [MainMenu::class, 'index'])->name('index');
    Route::get('/favorit', [MainMenu::class, 'favoritPengguna'])->name('favorit');
    Route::get('/hari-ini', [MainMenu::class, 'resepHariIni'])->name('hari-ini');
});

Route::get('/detail-resep', function () {
    return view('pages.detail_resep.detail_resep');
})->name('resep.detail');

Route::get('/timer-resep', function () {
    return view('pages.timer_resep.timer_resep');
})->name('resep.timer');

Route::get('/ulasan', function () {
    return view('pages.ulasan.ulasan');
})->name('resep.ulasan');

Route::middleware(['auth'])->group(function () {
    Route::post('/favorit/toggle/{id}', [FavoriteController::class, 'toggle'])->name('favorit.toggle');
});

Route::get('/pencarian-resep', [PencarianResepController::class, 'index'])->name('pencarian.resep');

Route::get('/swipe-rasa', [SwipeResepController::class, 'index'])->name('swipe.rasa');
Route::get('/filter-resep-swipe', [SwipeResepController::class, 'showFilter'])->name('swipe.filter');

Route::prefix('kulkas-digital')->name('kulkas.')->group(function () {
    Route::get('/', [KulkasDigitalController::class, 'index'])->name('index');
    Route::get('/tambah', [KulkasDigitalController::class, 'tambah'])->name('tambah');
    Route::post('/', [KulkasDigitalController::class, 'store'])->name('store');
    Route::delete('/{id}', [KulkasDigitalController::class, 'destroy'])->name('destroy');
});

Route::get('/meal-planner', [MealPlannerController::class, 'index'])->name('meal-planner.index');
Route::get('/pilih-resep', [PilihResepController::class, 'index'])->name('pilih-resep.index');
Route::get('/nota-belanja', [NotaBelAnjaController::class, 'index'])->name('nota.index');

Route::get('/profile', function () {
    return view('pages.profile.index');
})->name('profile.index');

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/resep/search', [ResepApiController::class, 'search'])->name('resep.search');
    Route::get('/bahan/by-ids', [ResepApiController::class, 'getBahansByIds'])->name('bahan.by-ids');
    Route::get('/bahans', [BahansController::class, 'apiList'])->name('bahans');

    Route::prefix('swipe')->name('swipe.')->group(function () {
        Route::get('/rasa', [SwipeResepApiController::class, 'getRasa'])->name('rasa');
        Route::get('/filter-resep-swipe', [SwipeResepApiController::class, 'filterSwipe'])->name('filter.resep.swipe');
    });
});

Route::prefix('admin/bahans')->name('admin.bahans.')->group(function () {
    Route::get('/', [BahansController::class, 'index'])->name('index');
    Route::get('/tambah', [BahansController::class, 'create'])->name('create');
    Route::post('/', [BahansController::class, 'store'])->name('store');
    Route::get('/{bahan}/edit', [BahansController::class, 'edit'])->name('edit');
    Route::put('/{bahan}', [BahansController::class, 'update'])->name('update');
    Route::delete('/{bahan}', [BahansController::class, 'destroy'])->name('destroy');
});