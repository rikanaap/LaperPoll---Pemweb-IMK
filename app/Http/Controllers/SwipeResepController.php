<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SwipeResepController extends Controller
{
    /**
     * ======================================================
     * HALAMAN SWIPE RASA
     * ======================================================
     */
    public function index(): View
    {
        $rasaDummy = [
            [
                'id' => 1,
                'title' => 'Pedas',
                'description' => 'Cocok buat kamu yang suka tantangan rasa membara 🔥',
                'icon' => 'local_fire_department',
                'gradient' => [
                    '#ff6b35',
                    '#ff3d00',
                ],
            ],

            [
                'id' => 2,
                'title' => 'Manis',
                'description' => 'Rasa lembut yang bikin mood jadi lebih baik 🍰',
                'icon' => 'cake',
                'gradient' => [
                    '#ec4899',
                    '#be185d',
                ],
            ],

            [
                'id' => 3,
                'title' => 'Gurih',
                'description' => 'Favorit semua orang, penuh cita rasa 🤤',
                'icon' => 'ramen_dining',
                'gradient' => [
                    '#10b981',
                    '#047857',
                ],
            ],

            [
                'id' => 4,
                'title' => 'Asin',
                'description' => 'Rasa klasik yang nggak pernah gagal 🧂',
                'icon' => 'restaurant',
                'gradient' => [
                    '#6366f1',
                    '#4338ca',
                ],
            ],

            [
                'id' => 5,
                'title' => 'Sehat',
                'description' => 'Pilihan ringan dan bergizi untuk tubuh 🥗',
                'icon' => 'eco',
                'gradient' => [
                    '#84cc16',
                    '#4d7c0f',
                ],
            ],
        ];

        return view(
            'pages.swipe-resep.index',
            compact('rasaDummy')
        );
    }

    /**
     * ======================================================
     * HALAMAN HASIL FILTER SWIPE
     * ======================================================
     */
    public function showFilter(): View
    {
        // Best Practice: Gunakan collection agar bisa diproses seperti data dari DB
        $resepList = collect([
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

        // Kirim variabel resepList ke view filter
        return view('pages.swipe-resep.filter.index', compact('resepList'));
    }
}