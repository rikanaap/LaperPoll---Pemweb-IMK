<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserFridge;
use Carbon\Carbon;

class UserFridgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [

            // Ikbal (user_id = 2)
            [
                'user_id' => 2,
                'bahan_id' => 3, // Telur
                'jumlah' => '6 butir',
                'bought_date' => Carbon::now()->subDays(2),
                'expired_date' => Carbon::now()->addDays(10),
            ],
            [
                'user_id' => 2,
                'bahan_id' => 5, // Bawang Merah
                'jumlah' => '250 gram',
                'bought_date' => Carbon::now()->subDays(3),
                'expired_date' => Carbon::now()->addDays(7),
            ],
            [
                'user_id' => 2,
                'bahan_id' => 1, // Beras
                'jumlah' => '2 kg',
                'bought_date' => Carbon::now()->subDays(7),
                'expired_date' => null,
            ],

            // Harmoni (user_id = 3)
            [
                'user_id' => 3,
                'bahan_id' => 2, // Mie
                'jumlah' => '3 bungkus',
                'bought_date' => Carbon::now()->subDays(1),
                'expired_date' => Carbon::now()->addDays(30),
            ],
            [
                'user_id' => 3,
                'bahan_id' => 6, // Cabai
                'jumlah' => '100 gram',
                'bought_date' => Carbon::now()->subDays(1),
                'expired_date' => Carbon::now()->addDays(5),
            ],
            [
                'user_id' => 3,
                'bahan_id' => 8, // Minyak Goreng
                'jumlah' => '1 liter',
                'bought_date' => Carbon::now()->subDays(10),
                'expired_date' => Carbon::now()->addDays(90),
            ],
        ];

        foreach ($items as $item) {
            UserFridge::create($item);
        }
    }
}
