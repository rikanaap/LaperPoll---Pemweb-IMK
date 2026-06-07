<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bahan;

class BahanSeeder extends Seeder
{
    public function run(): void
    {
        $bahans = [

            // ── KARBOHIDRAT ─────────────────────────────────────
            ['nama' => 'Nasi Putih',          'expired_expectancy_day' => 1],
            ['nama' => 'Beras',               'expired_expectancy_day' => 365],
            ['nama' => 'Mie Telur',           'expired_expectancy_day' => 180],
            ['nama' => 'Tepung Terigu',       'expired_expectancy_day' => 180],
            ['nama' => 'Tepung Beras',        'expired_expectancy_day' => 180],
            ['nama' => 'Tepung Ketan',        'expired_expectancy_day' => 180],
            ['nama' => 'Tepung Maizena',      'expired_expectancy_day' => 365],
            ['nama' => 'Kentang',             'expired_expectancy_day' => 20],

            // ── PROTEIN HEWANI ───────────────────────────────────
            ['nama' => 'Ayam',                'expired_expectancy_day' => 2],
            ['nama' => 'Daging Sapi',         'expired_expectancy_day' => 2],
            ['nama' => 'Buntut Sapi',         'expired_expectancy_day' => 2],
            ['nama' => 'Ikan Mas',            'expired_expectancy_day' => 1],
            ['nama' => 'Ikan Lele',           'expired_expectancy_day' => 1],
            ['nama' => 'Telur Ayam',          'expired_expectancy_day' => 14],
            ['nama' => 'Tahu Putih',          'expired_expectancy_day' => 3],
            ['nama' => 'Tempe',               'expired_expectancy_day' => 3],

            // ── SAYURAN ─────────────────────────────────────────
            ['nama' => 'Wortel',              'expired_expectancy_day' => 14],
            ['nama' => 'Kubis',               'expired_expectancy_day' => 7],
            ['nama' => 'Sawi Hijau',          'expired_expectancy_day' => 3],
            ['nama' => 'Kangkung',            'expired_expectancy_day' => 2],
            ['nama' => 'Kacang Panjang',      'expired_expectancy_day' => 5],
            ['nama' => 'Buncis',              'expired_expectancy_day' => 5],
            ['nama' => 'Labu Siam',           'expired_expectancy_day' => 14],
            ['nama' => 'Tauge',               'expired_expectancy_day' => 3],
            ['nama' => 'Tomat',               'expired_expectancy_day' => 5],
            ['nama' => 'Daun Bawang',         'expired_expectancy_day' => 4],
            ['nama' => 'Seledri',             'expired_expectancy_day' => 5],
            ['nama' => 'Kemangi',             'expired_expectancy_day' => 2],
            ['nama' => 'Timun',               'expired_expectancy_day' => 5],

            // ── BUAH ────────────────────────────────────────────
            ['nama' => 'Pisang Kepok',        'expired_expectancy_day' => 4],
            ['nama' => 'Alpukat',             'expired_expectancy_day' => 4],
            ['nama' => 'Jeruk Nipis',         'expired_expectancy_day' => 14],

            // ── BUMBU DASAR ──────────────────────────────────────
            ['nama' => 'Bawang Merah',        'expired_expectancy_day' => 30],
            ['nama' => 'Bawang Putih',        'expired_expectancy_day' => 30],
            ['nama' => 'Cabai Merah',         'expired_expectancy_day' => 7],
            ['nama' => 'Cabai Rawit',         'expired_expectancy_day' => 5],
            ['nama' => 'Kemiri',              'expired_expectancy_day' => 90],
            ['nama' => 'Ketumbar',            'expired_expectancy_day' => 180],
            ['nama' => 'Kunyit',              'expired_expectancy_day' => 20],
            ['nama' => 'Jahe',               'expired_expectancy_day' => 20],
            ['nama' => 'Lengkuas',            'expired_expectancy_day' => 20],
            ['nama' => 'Serai',               'expired_expectancy_day' => 10],
            ['nama' => 'Daun Salam',          'expired_expectancy_day' => 5],
            ['nama' => 'Daun Jeruk',          'expired_expectancy_day' => 5],
            ['nama' => 'Daun Pandan',         'expired_expectancy_day' => 5],
            ['nama' => 'Daun Kunyit',         'expired_expectancy_day' => 5],
            ['nama' => 'Terasi',              'expired_expectancy_day' => 180],

            // ── BUMBU OLAHAN ─────────────────────────────────────
            ['nama' => 'Kecap Manis',         'expired_expectancy_day' => 365],
            ['nama' => 'Saus Tiram',          'expired_expectancy_day' => 365],
            ['nama' => 'Garam',               'expired_expectancy_day' => 1825],
            ['nama' => 'Gula Pasir',          'expired_expectancy_day' => 730],
            ['nama' => 'Gula Merah',          'expired_expectancy_day' => 180],
            ['nama' => 'Merica Bubuk',        'expired_expectancy_day' => 365],
            ['nama' => 'Pala Bubuk',          'expired_expectancy_day' => 365],
            ['nama' => 'Kaldu Bubuk',         'expired_expectancy_day' => 365],
            ['nama' => 'Cengkeh',             'expired_expectancy_day' => 365],
            ['nama' => 'Coklat Bubuk',        'expired_expectancy_day' => 365],
            ['nama' => 'Agar-Agar Bubuk',     'expired_expectancy_day' => 365],

            // ── PRODUK SUSU & SANTAN ─────────────────────────────
            ['nama' => 'Santan',              'expired_expectancy_day' => 2],
            ['nama' => 'Susu Cair',           'expired_expectancy_day' => 5],
            ['nama' => 'Susu Kental Manis',   'expired_expectancy_day' => 365],

            // ── MINYAK & LEMAK ───────────────────────────────────
            ['nama' => 'Minyak Goreng',       'expired_expectancy_day' => 180],

            // ── BAHAN KERING & PELENGKAP ─────────────────────────
            ['nama' => 'Kacang Tanah',        'expired_expectancy_day' => 60],
            ['nama' => 'Kacang Hijau',        'expired_expectancy_day' => 180],
            ['nama' => 'Wijen',               'expired_expectancy_day' => 180],
            ['nama' => 'Kelapa Parut',        'expired_expectancy_day' => 2],
            ['nama' => 'Bawang Goreng',       'expired_expectancy_day' => 30],
            ['nama' => 'Cincau Hijau',        'expired_expectancy_day' => 2],
            ['nama' => 'Teh Celup',           'expired_expectancy_day' => 730],
            ['nama' => 'Es Batu',             'expired_expectancy_day' => 1],
        ];

        foreach ($bahans as $bahan) {
            Bahan::updateOrCreate(
                ['nama' => $bahan['nama']],
                $bahan
            );
        }
    }
}