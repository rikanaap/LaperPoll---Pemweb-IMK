<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    /**
     * Menampilkan daftar bahan yang dikelompokkan berdasarkan abjad.
     */
    public function index()
    {
        // Mengambil semua bahan diurutkan dari A-Z, lalu dikelompokkan berdasarkan huruf pertama
        $bahans = Bahan::orderBy('nama', 'asc')->get()->groupBy(function($item) {
            return strtoupper(substr($item->nama, 0, 1));
        });

        // Ubah path view sesuai dengan struktur folder proyekmu
        return view('pages.pencarian-resep.index', compact('bahans'));
    }
}