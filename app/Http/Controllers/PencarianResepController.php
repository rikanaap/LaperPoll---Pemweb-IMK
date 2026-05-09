<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PencarianResepController extends Controller
{
    public function index()
    {
        $bahans = collect([
            'A' => [(object)['id' => 1, 'nama' => 'Ayam'], (object)['id' => 2, 'nama' => 'Apel']],
            'B' => [(object)['id' => 3, 'nama' => 'Bawang Merah'], (object)['id' => 4, 'nama' => 'Beras']],
            'C' => [(object)['id' => 5, 'nama' => 'Cabai'], (object)['id' => 6, 'nama' => 'Cumi']],
        ]);

        $reseps = collect([
            (object)[
                'id' => 1,
                'user_id' => 1,
                'title' => 'Ayam Goreng Crispy',
                'cook_duration' => '30 menit',
                'current_star' => 4.8,
                'views_count' => 145,
                'thumbnail' => 'assets/images/nasi_goreng.jpeg', // Masih pakai icon default
                'user' => (object)['name' => 'Ikbal Miftahudin'],
            ],
            (object)[
                'id' => 2,
                'user_id' => 2,
                'title' => 'Sambal Bawang',
                'cook_duration' => '10 menit',
                'current_star' => 4.5,
                'views_count' => 89,
                'thumbnail' => 'assets/images/nasi_goreng.jpeg', // Masih pakai icon default
                'user' => (object)['name' => 'Admin Laperpoll'],
            ],
            (object)[
                'id' => 3,
                'user_id' => 3,
                'title' => 'Nasi Goreng Spesial',
                'cook_duration' => '20 menit',
                'current_star' => 4.2,
                'views_count' => 210,
                'thumbnail' => 'assets/images/nasi_goreng.jpeg', // <--- Panggil file lo
                'user' => (object)['name' => 'Chef Asep'],
            ],
              (object)[
                'id' => 3,
                'user_id' => 3,
                'title' => 'Nasi Goreng Spesial',
                'cook_duration' => '20 menit',
                'current_star' => 4.2,
                'views_count' => 210,
                'thumbnail' => 'assets/images/nasi_goreng.jpeg', // <--- Panggil file lo
                'user' => (object)['name' => 'Chef Asep'],
            ],
              (object)[
                'id' => 3,
                'user_id' => 3,
                'title' => 'Nasi Goreng Spesial',
                'cook_duration' => '20 menit',
                'current_star' => 4.2,
                'views_count' => 210,
                'thumbnail' => 'assets/images/nasi_goreng.jpeg', // <--- Panggil file lo
                'user' => (object)['name' => 'Chef Asep'],
            ],
              (object)[
                'id' => 3,
                'user_id' => 3,
                'title' => 'Nasi Goreng Spesial',
                'cook_duration' => '20 menit',
                'current_star' => 4.2,
                'views_count' => 210,
                'thumbnail' => 'assets/images/nasi_goreng.jpeg', // <--- Panggil file lo
                'user' => (object)['name' => 'Chef Asep'],
            ],
              (object)[
                'id' => 3,
                'user_id' => 3,
                'title' => 'Nasi Goreng Spesial',
                'cook_duration' => '20 menit',
                'current_star' => 4.2,
                'views_count' => 210,
                'thumbnail' => 'assets/images/nasi_goreng.jpeg', // <--- Panggil file lo
                'user' => (object)['name' => 'Chef Asep'],
            ],
        ]);

        return view('pages.pencarian-resep.index', compact('bahans', 'reseps'));
    }

    public function filter()
    {
        // Sama seperti index, sesuaikan jika perlu data berbeda untuk mobile
        $reseps = collect([
            (object)[
                'id' => 3,
                'user_id' => 3,
                'title' => 'Nasi Goreng Spesial',
                'cook_duration' => '20 menit',
                'current_star' => 4.2,
                'views_count' => 210,
                'thumbnail' => 'assets/images/nasi_goreng.jpeg',
                'user' => (object)['name' => 'Chef Asep'],
            ],
        ]);

        return view('pages.pencarian-resep.filter-resep.index', compact('reseps'));
    }
}