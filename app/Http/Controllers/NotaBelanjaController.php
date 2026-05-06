<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotaBelAnjaController extends Controller
{
    public function index()
    {
        return view('pages.nota_belanja.index');
    }
}