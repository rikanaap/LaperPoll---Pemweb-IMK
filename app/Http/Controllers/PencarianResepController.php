<?php

namespace App\Http\Controllers;

use App\Models\Bahan;

class PencarianResepController extends Controller
{
    public function index()
    {
        $bahans = Bahan::query()
            ->orderBy('nama')
            ->get()
            ->groupBy(function ($bahan) {
                return strtoupper(substr($bahan->nama, 0, 1));
            });

        return view(
            'pages.pencarian-resep.index',
            compact('bahans')
        );
    }

    public function filter()
    {
        return view(
            'pages.pencarian-resep.filter-resep.index'
        );
    }
}