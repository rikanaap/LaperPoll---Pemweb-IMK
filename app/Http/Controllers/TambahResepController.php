<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TambahResepController extends Controller
{
    public function show()
    {
        return view('pages.resep.tambah');
    }
}
