<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('user.landing');
});

Route::get('/detail-resep', function () {
    return view('pages.detail_resep.detail_resep');
});

Route::get('/main-menu', function () {
    return view('pages.main-menu.main-menu');
});
