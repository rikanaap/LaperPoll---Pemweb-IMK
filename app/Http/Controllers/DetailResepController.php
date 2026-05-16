<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;

class DetailResepController extends Controller
{

    public function showDetail($id)
    {
        $resep = Resep::findOrFail($id);
        return view('pages.detail_resep.detail_resep', compact('resep'));
    }
}
