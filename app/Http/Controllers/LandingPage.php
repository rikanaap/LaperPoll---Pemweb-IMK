<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPage extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reseps = Resep::get();
        $bahans = Bahan::get();
        return view('index', compact('user', 'reseps', 'bahans'));
    }
}
