<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserCart;

class UserCartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $carts = [

            // Ikbal (user_id = 2)
            [
                'user_id' => 2,
                'bahan_id' => 1, // Beras
                'gram_total' => 1000,
                'is_done' => true,
            ],
            [
                'user_id' => 2,
                'bahan_id' => 3, // Telur
                'gram_total' => 500,
                'is_done' => false,
            ],
            [
                'user_id' => 2,
                'bahan_id' => 5, // Bawang Merah
                'gram_total' => 250,
                'is_done' => false,
            ],

            // Harmoni (user_id = 3)
            [
                'user_id' => 3,
                'bahan_id' => 2, // Mie
                'gram_total' => 500,
                'is_done' => true,
            ],
            [
                'user_id' => 3,
                'bahan_id' => 8, // Minyak Goreng
                'gram_total' => 1000,
                'is_done' => false,
            ],
            [
                'user_id' => 3,
                'bahan_id' => 6, // Cabai
                'gram_total' => 200,
                'is_done' => false,
            ],
        ];

        foreach ($carts as $cart) {
            UserCart::create($cart);
        }
    }
}
