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
            // ── PROTEIN HEWANI ────────────────────────────────
            ['nama' => 'Telur',              'expired_expectancy_day' => 14],
            ['nama' => 'Ayam',               'expired_expectancy_day' => 3],
            ['nama' => 'Daging Sapi',        'expired_expectancy_day' => 3],
            ['nama' => 'Daging Kambing',     'expired_expectancy_day' => 3],
            ['nama' => 'Ikan',               'expired_expectancy_day' => 2],
            ['nama' => 'Ikan Salmon',        'expired_expectancy_day' => 2],
            ['nama' => 'Ikan Tuna',          'expired_expectancy_day' => 2],
            ['nama' => 'Udang',              'expired_expectancy_day' => 2],
            ['nama' => 'Cumi-cumi',          'expired_expectancy_day' => 2],
            ['nama' => 'Kepiting',           'expired_expectancy_day' => 2],
            ['nama' => 'Bakso',              'expired_expectancy_day' => 3],
            ['nama' => 'Sosis',              'expired_expectancy_day' => 7],
            ['nama' => 'Nugget Ayam',        'expired_expectancy_day' => 30],
            ['nama' => 'Hati Ayam',          'expired_expectancy_day' => 2],

            // ── PROTEIN NABATI ────────────────────────────────
            ['nama' => 'Tahu',               'expired_expectancy_day' => 3],
            ['nama' => 'Tempe',              'expired_expectancy_day' => 3],
            ['nama' => 'Oncom',              'expired_expectancy_day' => 2],

            // ── SAYURAN ───────────────────────────────────────
            ['nama' => 'Wortel',             'expired_expectancy_day' => 14],
            ['nama' => 'Kentang',            'expired_expectancy_day' => 30],
            ['nama' => 'Kubis',              'expired_expectancy_day' => 10],
            ['nama' => 'Bayam',              'expired_expectancy_day' => 3],
            ['nama' => 'Sawi',               'expired_expectancy_day' => 4],
            ['nama' => 'Kangkung',           'expired_expectancy_day' => 3],
            ['nama' => 'Buncis',             'expired_expectancy_day' => 7],
            ['nama' => 'Terong',             'expired_expectancy_day' => 7],
            ['nama' => 'Jagung',             'expired_expectancy_day' => 3],
            ['nama' => 'Labu Siam',          'expired_expectancy_day' => 10],
            ['nama' => 'Pare',               'expired_expectancy_day' => 7],
            ['nama' => 'Kacang Panjang',     'expired_expectancy_day' => 5],
            ['nama' => 'Paprika',            'expired_expectancy_day' => 7],
            ['nama' => 'Jamur',              'expired_expectancy_day' => 5],
            ['nama' => 'Brokoli',            'expired_expectancy_day' => 5],
            ['nama' => 'Kol Merah',          'expired_expectancy_day' => 14],
            ['nama' => 'Selada',             'expired_expectancy_day' => 3],
            ['nama' => 'Timun',              'expired_expectancy_day' => 7],

            // ── BUMBU & REMPAH ────────────────────────────────
            ['nama' => 'Bawang Merah',       'expired_expectancy_day' => 30],
            ['nama' => 'Bawang Putih',       'expired_expectancy_day' => 30],
            ['nama' => 'Bawang Bombay',      'expired_expectancy_day' => 30],
            ['nama' => 'Cabai Merah',        'expired_expectancy_day' => 7],
            ['nama' => 'Cabai Rawit',        'expired_expectancy_day' => 7],
            ['nama' => 'Cabai Hijau',        'expired_expectancy_day' => 7],
            ['nama' => 'Tomat',              'expired_expectancy_day' => 7],
            ['nama' => 'Jahe',               'expired_expectancy_day' => 30],
            ['nama' => 'Kunyit',             'expired_expectancy_day' => 30],
            ['nama' => 'Lengkuas',           'expired_expectancy_day' => 14],
            ['nama' => 'Serai',              'expired_expectancy_day' => 14],
            ['nama' => 'Daun Salam',         'expired_expectancy_day' => 7],
            ['nama' => 'Daun Jeruk',         'expired_expectancy_day' => 7],
            ['nama' => 'Daun Bawang',        'expired_expectancy_day' => 5],
            ['nama' => 'Seledri',            'expired_expectancy_day' => 5],
            ['nama' => 'Kemiri',             'expired_expectancy_day' => 90],
            ['nama' => 'Ketumbar',           'expired_expectancy_day' => 365],
            ['nama' => 'Jintan',             'expired_expectancy_day' => 365],
            ['nama' => 'Merica',             'expired_expectancy_day' => 365],

            // ── KARBOHIDRAT & TEPUNG ──────────────────────────
            ['nama' => 'Nasi',               'expired_expectancy_day' => 1],
            ['nama' => 'Beras',              'expired_expectancy_day' => 365],
            ['nama' => 'Mie',                'expired_expectancy_day' => 180],
            ['nama' => 'Mie Instan',         'expired_expectancy_day' => 365],
            ['nama' => 'Bihun',              'expired_expectancy_day' => 180],
            ['nama' => 'Soun',               'expired_expectancy_day' => 180],
            ['nama' => 'Makaroni',           'expired_expectancy_day' => 365],
            ['nama' => 'Roti Tawar',         'expired_expectancy_day' => 5],
            ['nama' => 'Tepung Terigu',      'expired_expectancy_day' => 180],
            ['nama' => 'Tepung Beras',       'expired_expectancy_day' => 180],
            ['nama' => 'Tepung Tapioka',     'expired_expectancy_day' => 365],
            ['nama' => 'Tepung Maizena',     'expired_expectancy_day' => 365],
            ['nama' => 'Oat',                'expired_expectancy_day' => 365],

            // ── SUSU & PRODUK OLAHAN ──────────────────────────
            ['nama' => 'Susu',               'expired_expectancy_day' => 7],
            ['nama' => 'Susu UHT',           'expired_expectancy_day' => 180],
            ['nama' => 'Susu Bubuk',         'expired_expectancy_day' => 365],
            ['nama' => 'Keju',               'expired_expectancy_day' => 30],
            ['nama' => 'Mentega',            'expired_expectancy_day' => 60],
            ['nama' => 'Yogurt',             'expired_expectancy_day' => 14],
            ['nama' => 'Krim',               'expired_expectancy_day' => 7],
            ['nama' => 'Santan',             'expired_expectancy_day' => 3],
            ['nama' => 'Santan Kemasan',     'expired_expectancy_day' => 365],

            // ── BUMBU MASAK & SAUS ────────────────────────────
            ['nama' => 'Garam',              'expired_expectancy_day' => 365],
            ['nama' => 'Gula Pasir',         'expired_expectancy_day' => 365],
            ['nama' => 'Gula Merah',         'expired_expectancy_day' => 180],
            ['nama' => 'Kecap Manis',        'expired_expectancy_day' => 180],
            ['nama' => 'Kecap Asin',         'expired_expectancy_day' => 365],
            ['nama' => 'Saus Sambal',        'expired_expectancy_day' => 180],
            ['nama' => 'Saus Tomat',         'expired_expectancy_day' => 180],
            ['nama' => 'Saus Tiram',         'expired_expectancy_day' => 365],
            ['nama' => 'Minyak Goreng',      'expired_expectancy_day' => 180],
            ['nama' => 'Minyak Wijen',       'expired_expectancy_day' => 180],
            ['nama' => 'Minyak Zaitun',      'expired_expectancy_day' => 365],
            ['nama' => 'Cuka',               'expired_expectancy_day' => 365],
            ['nama' => 'Terasi',             'expired_expectancy_day' => 180],
            ['nama' => 'Petis',              'expired_expectancy_day' => 90],
            ['nama' => 'Tauco',              'expired_expectancy_day' => 90],
            ['nama' => 'Bumbu Kari',         'expired_expectancy_day' => 365],
            ['nama' => 'Kaldu Bubuk',        'expired_expectancy_day' => 365],

            // ── BUAH ──────────────────────────────────────────
            ['nama' => 'Pisang',             'expired_expectancy_day' => 5],
            ['nama' => 'Apel',               'expired_expectancy_day' => 14],
            ['nama' => 'Jeruk',              'expired_expectancy_day' => 7],
            ['nama' => 'Mangga',             'expired_expectancy_day' => 5],
            ['nama' => 'Semangka',           'expired_expectancy_day' => 3],
            ['nama' => 'Melon',              'expired_expectancy_day' => 5],
            ['nama' => 'Pepaya',             'expired_expectancy_day' => 5],
            ['nama' => 'Nanas',              'expired_expectancy_day' => 5],
            ['nama' => 'Anggur',             'expired_expectancy_day' => 7],
            ['nama' => 'Stroberi',           'expired_expectancy_day' => 3],
            ['nama' => 'Alpukat',            'expired_expectancy_day' => 3],
            ['nama' => 'Kelapa',             'expired_expectancy_day' => 7],
            ['nama' => 'Lemon',              'expired_expectancy_day' => 14],
        ];

        foreach ($bahans as $bahan) {
            // Gunakan firstOrCreate supaya tidak duplikat kalau seeder dijalankan ulang
            Bahan::firstOrCreate(
                ['nama' => $bahan['nama']],
                ['expired_expectancy_day' => $bahan['expired_expectancy_day']]
            );
        }
    }
}