<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserFridge;
use Carbon\Carbon;

class UserFridgeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [

            // ── Ikbal (user_id = 2) ────────────────────────────
            [
                'user_id'      => 2,
                'bahan_id'     => 3,    // Telur Ayam
                'jumlah'       => 6,    // 6 butir
                'bought_date'  => Carbon::now()->subDays(2),
                'expired_date' => Carbon::now()->addDays(10),
            ],
            [
                'user_id'      => 2,
                'bahan_id'     => 5,    // Bawang Merah
                'jumlah'       => 250,  // 250 gram
                'bought_date'  => Carbon::now()->subDays(3),
                'expired_date' => Carbon::now()->addDays(27),
            ],
            [
                'user_id'      => 2,
                'bahan_id'     => 1,    // Beras
                'jumlah'       => 2000, // 2 kg = 2000 gram
                'bought_date'  => Carbon::now()->subDays(7),
                'expired_date' => Carbon::now()->addDays(358),
            ],
            [
                'user_id'      => 2,
                'bahan_id'     => 4,    // Ayam
                'jumlah'       => 500,  // 500 gram
                'bought_date'  => Carbon::now()->subDays(1),
                'expired_date' => Carbon::now()->addDays(1),
            ],
            [
                'user_id'      => 2,
                'bahan_id'     => 9,    // Kecap Manis
                'jumlah'       => 135,  // 135 ml (1 botol kecil)
                'bought_date'  => Carbon::now()->subDays(14),
                'expired_date' => Carbon::now()->addDays(351),
            ],

            // ── Harmoni (user_id = 3) ──────────────────────────
            [
                'user_id'      => 3,
                'bahan_id'     => 2,    // Mie Telur
                'jumlah'       => 3,    // 3 bungkus (satuan bungkus ±85g)
                'bought_date'  => Carbon::now()->subDays(1),
                'expired_date' => Carbon::now()->addDays(179),
            ],
            [
                'user_id'      => 3,
                'bahan_id'     => 7,    // Cabai Merah
                'jumlah'       => 100,  // 100 gram
                'bought_date'  => Carbon::now()->subDays(1),
                'expired_date' => Carbon::now()->addDays(5),
            ],
            [
                'user_id'      => 3,
                'bahan_id'     => 6,    // Bawang Putih
                'jumlah'       => 150,  // 150 gram
                'bought_date'  => Carbon::now()->subDays(5),
                'expired_date' => Carbon::now()->addDays(25),
            ],
            [
                'user_id'      => 3,
                'bahan_id'     => 3,    // Telur Ayam
                'jumlah'       => 12,   // 12 butir (1 lusin)
                'bought_date'  => Carbon::now()->subDays(3),
                'expired_date' => Carbon::now()->addDays(11),
            ],
            [
                'user_id'      => 3,
                'bahan_id'     => 17,   // Santan
                'jumlah'       => 200,  // 200 ml (1 sachet)
                'bought_date'  => Carbon::now()->subDays(1),
                'expired_date' => Carbon::now()->addDays(1),
            ],

            // ── User lain (user_id = 4) ────────────────────────
            [
                'user_id'      => 4,
                'bahan_id'     => 30,   // Daging Sapi
                'jumlah'       => 300,  // 300 gram
                'bought_date'  => Carbon::now()->subDays(1),
                'expired_date' => Carbon::now()->addDays(1),
            ],
            [
                'user_id'      => 4,
                'bahan_id'     => 5,    // Bawang Merah
                'jumlah'       => 100,
                'bought_date'  => Carbon::now()->subDays(4),
                'expired_date' => Carbon::now()->addDays(26),
            ],
            [
                'user_id'      => 4,
                'bahan_id'     => 6,    // Bawang Putih
                'jumlah'       => 80,
                'bought_date'  => Carbon::now()->subDays(4),
                'expired_date' => Carbon::now()->addDays(26),
            ],
            [
                'user_id'      => 4,
                'bahan_id'     => 13,   // Jahe
                'jumlah'       => 50,
                'bought_date'  => Carbon::now()->subDays(2),
                'expired_date' => Carbon::now()->addDays(18),
            ],
        ];

        foreach ($items as $item) {
            UserFridge::create($item);
        }
    }
}