<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use Illuminate\Http\Request;

class SwipeResepController extends Controller
{
    /**
     * Tampilkan halaman swipe resep dengan data filter rasa (level 3).
     */
    public function index()
    {
        // Mengambil data dari tabel filters hanya yang level 3
        $listRasa = Filter::where('level', 3)->get();

        return view('pages.swipe-resep.index', compact('listRasa'));
    }
}