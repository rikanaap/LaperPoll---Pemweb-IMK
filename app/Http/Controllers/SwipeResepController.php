<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SwipeResepController extends Controller
{
    public function index(): View
    {
        return view('pages.swipe-resep.index');
    }

    public function showFilter(): View
    {
        return view('pages.swipe-resep.filter.index');
    }
}