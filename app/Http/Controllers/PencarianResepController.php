<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PencarianResepController extends Controller
{
    /**
     * Halaman pencarian resep
     */
    public function index()
    {
        // =========================
        // DATA BAHAN
        // =========================
        $bahans = collect([
            'A' => [
                (object)[
                    'id' => 1,
                    'nama' => 'Ayam',
                ],
                (object)[
                    'id' => 2,
                    'nama' => 'Apel',
                ],
            ],

            'B' => [
                (object)[
                    'id' => 3,
                    'nama' => 'Bawang Merah',
                ],
                (object)[
                    'id' => 4,
                    'nama' => 'Beras',
                ],
            ],

            'C' => [
                (object)[
                    'id' => 5,
                    'nama' => 'Cabai',
                ],
                (object)[
                    'id' => 6,
                    'nama' => 'Cumi',
                ],
            ],
        ]);

        // =========================
        // DATA RESEP
        // =========================
        $reseps = collect([

            (object)[
                'title' => 'Ayam Goreng Crispy',
                'cook_duration' => '30 menit',
                'user' => (object)[
                    'name' => 'Ikbal',
                ],
            ],

            (object)[
                'title' => 'Sambal Bawang',
                'cook_duration' => '10 menit',
                'user' => (object)[
                    'name' => 'Admin',
                ],
            ],

            (object)[
                'title' => 'Nasi Goreng Spesial',
                'cook_duration' => '20 menit',
                'user' => (object)[
                    'name' => 'Chef Asep',
                ],
            ],

        ]);

        return view(
            'pages.pencarian-resep.index',
            compact('bahans', 'reseps')
        );
    }

    /**
     * Halaman filter resep mobile
     */
    public function filter()
    {
        // dummy sementara
        $reseps = collect([

            (object)[
                'title' => 'Ayam Goreng Crispy',
                'cook_duration' => '30 menit',
                'user' => (object)[
                    'name' => 'Ikbal',
                ],
            ],

            (object)[
                'title' => 'Sambal Bawang',
                'cook_duration' => '10 menit',
                'user' => (object)[
                    'name' => 'Admin',
                ],
            ],

        ]);

        return view(
            'pages.pencarian-resep.filter-resep.index',
            compact('reseps')
        );
    }
}