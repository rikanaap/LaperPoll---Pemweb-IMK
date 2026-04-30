<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Favorite;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $favorites = [

            // Ikbal suka resep
            [
                'user_id' => 2,
                'resep_id' => 1, // Nasi Goreng
            ],
            [
                'user_id' => 2,
                'resep_id' => 4, // Ayam Kecap
            ],

            // Harmoni suka resep
            [
                'user_id' => 3,
                'resep_id' => 1, // Nasi Goreng
            ],
            [
                'user_id' => 3,
                'resep_id' => 5, // Capcay
            ],
            [
                'user_id' => 3,
                'resep_id' => 3, // Telur Dadar
            ],

        ];

        foreach ($favorites as $favorite) {
            Favorite::create($favorite);
        }
    }
}
