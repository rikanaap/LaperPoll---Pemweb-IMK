<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bahan;

class BahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bahans = [
            ['nama' => 'Telur', 'expired_expectancy_day' => 14],
            ['nama' => 'Ayam', 'expired_expectancy_day' => 3],
            ['nama' => 'Daging Sapi', 'expired_expectancy_day' => 3],
            ['nama' => 'Ikan', 'expired_expectancy_day' => 2],
            ['nama' => 'Udang', 'expired_expectancy_day' => 2],

            ['nama' => 'Bawang Merah', 'expired_expectancy_day' => 30],
            ['nama' => 'Bawang Putih', 'expired_expectancy_day' => 30],
            ['nama' => 'Cabai Merah', 'expired_expectancy_day' => 7],
            ['nama' => 'Cabai Rawit', 'expired_expectancy_day' => 7],
            ['nama' => 'Tomat', 'expired_expectancy_day' => 7],

            ['nama' => 'Wortel', 'expired_expectancy_day' => 14],
            ['nama' => 'Kentang', 'expired_expectancy_day' => 30],
            ['nama' => 'Kubis', 'expired_expectancy_day' => 10],
            ['nama' => 'Bayam', 'expired_expectancy_day' => 3],
            ['nama' => 'Sawi', 'expired_expectancy_day' => 4],

            ['nama' => 'Nasi', 'expired_expectancy_day' => 1],
            ['nama' => 'Mie', 'expired_expectancy_day' => 180],
            ['nama' => 'Tepung Terigu', 'expired_expectancy_day' => 180],
            ['nama' => 'Roti Tawar', 'expired_expectancy_day' => 5],

            ['nama' => 'Susu', 'expired_expectancy_day' => 7],
            ['nama' => 'Keju', 'expired_expectancy_day' => 30],
            ['nama' => 'Mentega', 'expired_expectancy_day' => 60],

            ['nama' => 'Garam', 'expired_expectancy_day' => 365],
            ['nama' => 'Gula', 'expired_expectancy_day' => 365],
            ['nama' => 'Kecap Manis', 'expired_expectancy_day' => 180],
            ['nama' => 'Saus Sambal', 'expired_expectancy_day' => 180],
            ['nama' => 'Minyak Goreng', 'expired_expectancy_day' => 180],
        ];

        foreach ($bahans as $bahan) {
            Bahan::create($bahan);
        }
    }
}
