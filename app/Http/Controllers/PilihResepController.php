<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;

class PilihResepController extends Controller
{
    public function index(Request $request)
    {
        // slot format: "2026-05-27__sarapan"
        $slot  = $request->query('slot', '');
        $reseps = Resep::where('is_published', 1)
            ->select('id','title','calorie','cook_duration','thumbnail')
            ->orderBy('title')
            ->get();

        return view('pages.pilih-resep.index', compact('reseps', 'slot'));
    }
}