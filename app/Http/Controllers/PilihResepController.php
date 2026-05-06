<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;

class PilihResepController extends Controller
{
    public function index()
    {
        $reseps = Resep::with('attachments')->get();
        return view('pages.pilih-resep.index', compact('reseps'));
    }
}