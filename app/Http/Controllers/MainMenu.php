<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;

class MainMenu extends Controller
{
    public function index()
    {
        $reseps = Resep::with('user')->get();
        return view('pages.main-menu.main-menu', compact('reseps'));
    }
    public function favoritPengguna()
    {
        return view('pages.main-menu.main-menu');
    }
    public function resepHariIni()
    {
        return view('pages.main-menu.main-menu');
    }
}
