<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resep;
use App\Models\Bahan;
use App\Models\ResepBahan;

class ResepBahanSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |----------------------------------------------------------------------
        | Data gram_total di sini merepresentasikan bahan mentah/segar sebelum
        | dimasak untuk 1 porsi saji (kecuali resep yang memang dibuat lebih).
        |
        | Catatan:
        |  - Bumbu kecil (garam, merica, gula) dalam gram yang realistis.
        |  - Protein & karbohidrat mengacu pada porsi standar rumahan Indonesia.
        |----------------------------------------------------------------------
        */

        $data = [

            // ── MAKANAN BERAT ──────────────────────────────────────────────

            'Nasi Goreng Spesial' => [
                // ~1-2 porsi
                ['nama' => 'Nasi Putih',       'gram' => 300],  // 1,5 centong nasi
                ['nama' => 'Telur Ayam',       'gram' => 60],   // 1 butir
                ['nama' => 'Ayam',             'gram' => 80],   // suwiran dada ayam rebus
                ['nama' => 'Bawang Merah',     'gram' => 30],   // 4 siung
                ['nama' => 'Bawang Putih',     'gram' => 15],   // 3 siung
                ['nama' => 'Cabai Merah',      'gram' => 20],   // 2 buah
                ['nama' => 'Kecap Manis',      'gram' => 25],   // 2 sdm
                ['nama' => 'Garam',            'gram' => 4],
                ['nama' => 'Merica Bubuk',     'gram' => 2],
                ['nama' => 'Minyak Goreng',    'gram' => 20],
                ['nama' => 'Bawang Goreng',    'gram' => 10],   // taburan
                ['nama' => 'Daun Bawang',      'gram' => 10],
            ],

            'Mie Goreng Jawa' => [
                ['nama' => 'Mie Telur',        'gram' => 100],  // 1 bungkus kering
                ['nama' => 'Telur Ayam',       'gram' => 60],   // 1 butir
                ['nama' => 'Kubis',            'gram' => 50],
                ['nama' => 'Daun Bawang',      'gram' => 15],
                ['nama' => 'Bawang Putih',     'gram' => 15],   // 3 siung
                ['nama' => 'Cabai Merah',      'gram' => 15],
                ['nama' => 'Kecap Manis',      'gram' => 30],   // 2,5 sdm
                ['nama' => 'Garam',            'gram' => 3],
                ['nama' => 'Merica Bubuk',     'gram' => 2],
                ['nama' => 'Minyak Goreng',    'gram' => 20],
                ['nama' => 'Bawang Goreng',    'gram' => 8],
                ['nama' => 'Tomat',            'gram' => 30],   // garnish
            ],

            'Telur Dadar Crispy' => [
                ['nama' => 'Telur Ayam',       'gram' => 120],  // 2 butir
                ['nama' => 'Daun Bawang',      'gram' => 10],
                ['nama' => 'Bawang Putih',     'gram' => 5],    // 1 siung, optional
                ['nama' => 'Garam',            'gram' => 3],
                ['nama' => 'Merica Bubuk',     'gram' => 1],
                ['nama' => 'Minyak Goreng',    'gram' => 40],   // agak banyak untuk crispy
            ],

            'Ayam Kecap Pedas' => [
                ['nama' => 'Ayam',             'gram' => 400],  // 4 potong ayam
                ['nama' => 'Bawang Merah',     'gram' => 40],   // 5 siung
                ['nama' => 'Bawang Putih',     'gram' => 20],   // 4 siung
                ['nama' => 'Cabai Merah',      'gram' => 40],   // 4 buah
                ['nama' => 'Cabai Rawit',      'gram' => 15],   // 5 buah
                ['nama' => 'Kecap Manis',      'gram' => 40],   // 3 sdm
                ['nama' => 'Daun Salam',       'gram' => 3],    // 2 lembar
                ['nama' => 'Tomat',            'gram' => 50],
                ['nama' => 'Daun Bawang',      'gram' => 15],
                ['nama' => 'Garam',            'gram' => 4],
                ['nama' => 'Gula Pasir',       'gram' => 8],
                ['nama' => 'Jeruk Nipis',      'gram' => 15],   // perasan
                ['nama' => 'Minyak Goreng',    'gram' => 25],
            ],

            'Capcay Sayur' => [
                ['nama' => 'Wortel',           'gram' => 80],
                ['nama' => 'Kubis',            'gram' => 80],
                ['nama' => 'Sawi Hijau',       'gram' => 80],
                ['nama' => 'Buncis',           'gram' => 50],
                ['nama' => 'Bawang Putih',     'gram' => 15],   // 3 siung
                ['nama' => 'Bawang Merah',     'gram' => 20],
                ['nama' => 'Saus Tiram',       'gram' => 20],   // 2 sdm
                ['nama' => 'Garam',            'gram' => 4],
                ['nama' => 'Merica Bubuk',     'gram' => 2],
                ['nama' => 'Kaldu Bubuk',      'gram' => 4],
                ['nama' => 'Minyak Goreng',    'gram' => 20],
            ],

            'Soto Ayam' => [
                // resep untuk 4 porsi
                ['nama' => 'Ayam',             'gram' => 600],  // 1/2 ekor
                ['nama' => 'Bawang Merah',     'gram' => 60],   // 8 siung
                ['nama' => 'Bawang Putih',     'gram' => 30],   // 6 siung
                ['nama' => 'Kunyit',           'gram' => 20],   // 2 ruas
                ['nama' => 'Kemiri',           'gram' => 20],   // 3 butir
                ['nama' => 'Jahe',             'gram' => 15],   // 1 ruas
                ['nama' => 'Serai',            'gram' => 20],   // 2 batang
                ['nama' => 'Daun Salam',       'gram' => 4],    // 3 lembar
                ['nama' => 'Daun Jeruk',       'gram' => 3],    // 4 lembar
                ['nama' => 'Tauge',            'gram' => 80],
                ['nama' => 'Telur Ayam',       'gram' => 240],  // 4 butir rebus
                ['nama' => 'Garam',            'gram' => 10],
                ['nama' => 'Gula Pasir',       'gram' => 5],
                ['nama' => 'Minyak Goreng',    'gram' => 25],
                ['nama' => 'Bawang Goreng',    'gram' => 20],
                ['nama' => 'Seledri',          'gram' => 10],
            ],

            'Rendang Daging' => [
                // resep untuk 6 porsi, masak lama
                ['nama' => 'Daging Sapi',      'gram' => 1000], // 1 kg
                ['nama' => 'Santan',           'gram' => 600],  // 3 gelas santan kental
                ['nama' => 'Cabai Merah',      'gram' => 100],  // 10 buah
                ['nama' => 'Bawang Merah',     'gram' => 80],   // 10 siung
                ['nama' => 'Bawang Putih',     'gram' => 40],   // 8 siung
                ['nama' => 'Jahe',             'gram' => 20],   // 2 ruas
                ['nama' => 'Kunyit',           'gram' => 15],
                ['nama' => 'Lengkuas',         'gram' => 20],   // 2 ruas
                ['nama' => 'Serai',            'gram' => 30],   // 3 batang
                ['nama' => 'Daun Jeruk',       'gram' => 5],    // 6 lembar
                ['nama' => 'Daun Kunyit',      'gram' => 5],    // 2 lembar
                ['nama' => 'Kemiri',           'gram' => 25],   // 4 butir
                ['nama' => 'Garam',            'gram' => 15],
                ['nama' => 'Gula Merah',       'gram' => 20],
            ],

            'Opor Ayam' => [
                // resep untuk 4 porsi
                ['nama' => 'Ayam',             'gram' => 800],  // 1 ekor potong
                ['nama' => 'Santan',           'gram' => 500],  // santan encer + kental
                ['nama' => 'Bawang Merah',     'gram' => 60],   // 8 siung
                ['nama' => 'Bawang Putih',     'gram' => 30],   // 6 siung
                ['nama' => 'Kemiri',           'gram' => 25],   // 4 butir, sangrai
                ['nama' => 'Ketumbar',         'gram' => 8],    // 1 sdm
                ['nama' => 'Kunyit',           'gram' => 10],
                ['nama' => 'Serai',            'gram' => 20],   // 2 batang
                ['nama' => 'Daun Salam',       'gram' => 4],    // 3 lembar
                ['nama' => 'Daun Jeruk',       'gram' => 3],    // 4 lembar
                ['nama' => 'Garam',            'gram' => 10],
                ['nama' => 'Gula Pasir',       'gram' => 8],
                ['nama' => 'Minyak Goreng',    'gram' => 25],
                ['nama' => 'Bawang Goreng',    'gram' => 15],
            ],

            'Nasi Uduk' => [
                // resep untuk 4 porsi
                ['nama' => 'Beras',            'gram' => 400],  // 2 cup beras
                ['nama' => 'Santan',           'gram' => 350],  // santan dari 1 butir kelapa
                ['nama' => 'Serai',            'gram' => 15],   // 1 batang geprek
                ['nama' => 'Daun Salam',       'gram' => 3],    // 2 lembar
                ['nama' => 'Daun Pandan',      'gram' => 5],    // 1 lembar, ikat simpul
                ['nama' => 'Garam',            'gram' => 8],
                ['nama' => 'Bawang Goreng',    'gram' => 15],   // taburan
            ],

            'Gado-gado' => [
                // resep untuk 2 porsi
                ['nama' => 'Kentang',          'gram' => 150],  // 1 buah sedang
                ['nama' => 'Wortel',           'gram' => 80],
                ['nama' => 'Buncis',           'gram' => 60],
                ['nama' => 'Tauge',            'gram' => 80],
                ['nama' => 'Tahu Putih',       'gram' => 100],  // 2 potong
                ['nama' => 'Tempe',            'gram' => 100],  // 2 potong
                ['nama' => 'Telur Ayam',       'gram' => 120],  // 2 butir rebus
                ['nama' => 'Kacang Tanah',     'gram' => 150],  // kacang goreng untuk saus
                ['nama' => 'Cabai Merah',      'gram' => 25],
                ['nama' => 'Bawang Putih',     'gram' => 10],
                ['nama' => 'Gula Merah',       'gram' => 40],
                ['nama' => 'Kecap Manis',      'gram' => 20],
                ['nama' => 'Jeruk Nipis',      'gram' => 20],
                ['nama' => 'Garam',            'gram' => 5],
                ['nama' => 'Minyak Goreng',    'gram' => 30],
            ],

            // ── CAMILAN ────────────────────────────────────────────────────

            'Pisang Goreng Crispy' => [
                ['nama' => 'Pisang Kepok',     'gram' => 300],  // 3 buah pisang matang
                ['nama' => 'Tepung Terigu',    'gram' => 100],
                ['nama' => 'Tepung Beras',     'gram' => 50],   // kunci kerenyahan
                ['nama' => 'Gula Pasir',       'gram' => 15],
                ['nama' => 'Garam',            'gram' => 3],
                ['nama' => 'Minyak Goreng',    'gram' => 300],  // untuk menggoreng
            ],

            'Tahu Crispy' => [
                ['nama' => 'Tahu Putih',       'gram' => 300],  // 3 potong besar
                ['nama' => 'Tepung Maizena',   'gram' => 50],   // balutan tipis
                ['nama' => 'Bawang Putih',     'gram' => 10],
                ['nama' => 'Garam',            'gram' => 4],
                ['nama' => 'Merica Bubuk',     'gram' => 2],
                ['nama' => 'Minyak Goreng',    'gram' => 200],
            ],

            'Tempe Mendoan' => [
                ['nama' => 'Tempe',            'gram' => 250],  // 1 papan tempe tipis
                ['nama' => 'Tepung Terigu',    'gram' => 100],
                ['nama' => 'Bawang Putih',     'gram' => 10],
                ['nama' => 'Ketumbar',         'gram' => 4],    // 1/2 sdt
                ['nama' => 'Daun Bawang',      'gram' => 15],
                ['nama' => 'Garam',            'gram' => 4],
                ['nama' => 'Minyak Goreng',    'gram' => 150],
            ],

            'Bakwan Sayur' => [
                ['nama' => 'Wortel',           'gram' => 80],
                ['nama' => 'Kubis',            'gram' => 80],
                ['nama' => 'Daun Bawang',      'gram' => 20],
                ['nama' => 'Tepung Terigu',    'gram' => 120],
                ['nama' => 'Telur Ayam',       'gram' => 60],   // 1 butir
                ['nama' => 'Garam',            'gram' => 4],
                ['nama' => 'Merica Bubuk',     'gram' => 2],
                ['nama' => 'Kaldu Bubuk',      'gram' => 4],
                ['nama' => 'Minyak Goreng',    'gram' => 200],
            ],

            'Onde-onde' => [
                // resep untuk ±15 biji
                ['nama' => 'Tepung Ketan',     'gram' => 250],
                ['nama' => 'Kacang Hijau',     'gram' => 150],  // isian
                ['nama' => 'Gula Pasir',       'gram' => 60],
                ['nama' => 'Wijen',            'gram' => 50],   // balutan luar
                ['nama' => 'Garam',            'gram' => 3],
                ['nama' => 'Minyak Goreng',    'gram' => 300],  // api kecil, banyak minyak
            ],

            // ── MINUMAN ────────────────────────────────────────────────────

            'Es Teh Manis' => [
                ['nama' => 'Teh Celup',        'gram' => 4],    // 2 kantong (2g/kantong)
                ['nama' => 'Gula Pasir',       'gram' => 25],   // 2,5 sdm sesuai selera
                ['nama' => 'Es Batu',          'gram' => 150],
            ],

            'Jus Alpukat' => [
                ['nama' => 'Alpukat',          'gram' => 200],  // 1 buah besar
                ['nama' => 'Susu Kental Manis','gram' => 40],   // 2 sdm
                ['nama' => 'Susu Cair',        'gram' => 150],
                ['nama' => 'Gula Pasir',       'gram' => 15],
                ['nama' => 'Es Batu',          'gram' => 100],
            ],

            'Es Cincau Hijau' => [
                ['nama' => 'Cincau Hijau',     'gram' => 200],
                ['nama' => 'Santan',           'gram' => 200],  // santan segar
                ['nama' => 'Gula Merah',       'gram' => 80],   // sirop
                ['nama' => 'Daun Pandan',      'gram' => 5],    // untuk sirop dan santan
                ['nama' => 'Garam',            'gram' => 2],    // sejumput untuk santan
                ['nama' => 'Es Batu',          'gram' => 200],
            ],

            // ── DESSERT ────────────────────────────────────────────────────

            'Klepon' => [
                // resep untuk ±20 biji
                ['nama' => 'Tepung Ketan',     'gram' => 250],
                ['nama' => 'Gula Merah',       'gram' => 100],  // isian, serut kasar
                ['nama' => 'Daun Pandan',      'gram' => 8],    // diblender + air untuk pewarna
                ['nama' => 'Kelapa Parut',     'gram' => 150],  // dikukus + garam
                ['nama' => 'Garam',            'gram' => 3],
            ],

            'Bubur Sumsum' => [
                // resep untuk 4 porsi
                ['nama' => 'Tepung Beras',     'gram' => 100],
                ['nama' => 'Santan',           'gram' => 400],  // santan segar
                ['nama' => 'Daun Pandan',      'gram' => 5],    // 2 lembar
                ['nama' => 'Gula Merah',       'gram' => 150],  // kuah gula merah
                ['nama' => 'Garam',            'gram' => 4],
            ],

            'Puding Coklat' => [
                // resep untuk 1 loyang / 6 porsi
                ['nama' => 'Agar-Agar Bubuk',  'gram' => 14],   // 2 sachet 7g
                ['nama' => 'Coklat Bubuk',     'gram' => 40],
                ['nama' => 'Gula Pasir',       'gram' => 100],
                ['nama' => 'Susu Cair',        'gram' => 800],  // 800 ml
            ],

            // ── TAMBAHAN MAKANAN BERAT ─────────────────────────────────────

            'Ayam Bakar Bumbu Rujak' => [
                // resep untuk 4 potong ayam
                ['nama' => 'Ayam',             'gram' => 800],  // 4 potong (paha+dada)
                ['nama' => 'Cabai Merah',      'gram' => 60],   // 6 buah
                ['nama' => 'Cabai Rawit',      'gram' => 20],   // 7 buah
                ['nama' => 'Bawang Merah',     'gram' => 50],   // 6 siung
                ['nama' => 'Bawang Putih',     'gram' => 25],   // 5 siung
                ['nama' => 'Kemiri',           'gram' => 20],   // 3 butir, sangrai
                ['nama' => 'Gula Merah',       'gram' => 40],
                ['nama' => 'Garam',            'gram' => 8],
                ['nama' => 'Minyak Goreng',    'gram' => 20],
                ['nama' => 'Kemangi',          'gram' => 15],   // lalapan
                ['nama' => 'Timun',            'gram' => 80],   // lalapan
            ],

            'Ikan Goreng Sambal' => [
                ['nama' => 'Ikan Mas',         'gram' => 500],  // 1 ekor sedang
                ['nama' => 'Kunyit',           'gram' => 10],
                ['nama' => 'Jeruk Nipis',      'gram' => 20],
                ['nama' => 'Cabai Merah',      'gram' => 50],   // sambal
                ['nama' => 'Cabai Rawit',      'gram' => 20],   // sambal pedas
                ['nama' => 'Bawang Merah',     'gram' => 40],
                ['nama' => 'Tomat',            'gram' => 60],
                ['nama' => 'Terasi',           'gram' => 8],
                ['nama' => 'Garam',            'gram' => 6],
                ['nama' => 'Gula Pasir',       'gram' => 5],
                ['nama' => 'Minyak Goreng',    'gram' => 300],
                ['nama' => 'Kemangi',          'gram' => 15],   // lalapan
                ['nama' => 'Timun',            'gram' => 60],
            ],

            'Tumis Kangkung' => [
                ['nama' => 'Kangkung',         'gram' => 300],  // 1 ikat
                ['nama' => 'Bawang Putih',     'gram' => 15],   // 3 siung, geprek
                ['nama' => 'Cabai Rawit',      'gram' => 15],   // 5 buah, iris
                ['nama' => 'Saus Tiram',       'gram' => 15],   // 1,5 sdm
                ['nama' => 'Garam',            'gram' => 3],
                ['nama' => 'Gula Pasir',       'gram' => 4],
                ['nama' => 'Minyak Goreng',    'gram' => 20],
            ],

            'Sop Buntut' => [
                // resep untuk 4 porsi, dimasak lama
                ['nama' => 'Buntut Sapi',      'gram' => 800],  // ±6 ruas
                ['nama' => 'Wortel',           'gram' => 150],  // 2 buah
                ['nama' => 'Kentang',          'gram' => 200],  // 2 buah sedang
                ['nama' => 'Bawang Putih',     'gram' => 20],   // 4 siung
                ['nama' => 'Jahe',             'gram' => 20],   // geprek
                ['nama' => 'Pala Bubuk',       'gram' => 3],
                ['nama' => 'Merica Bubuk',     'gram' => 4],
                ['nama' => 'Garam',            'gram' => 12],
                ['nama' => 'Seledri',          'gram' => 15],
                ['nama' => 'Bawang Goreng',    'gram' => 20],
                ['nama' => 'Daun Bawang',      'gram' => 15],
            ],

            'Pecel Lele' => [
                ['nama' => 'Ikan Lele',        'gram' => 400],  // 2 ekor lele segar
                ['nama' => 'Kunyit',           'gram' => 8],
                ['nama' => 'Jeruk Nipis',      'gram' => 20],
                ['nama' => 'Cabai Merah',      'gram' => 50],
                ['nama' => 'Bawang Merah',     'gram' => 40],
                ['nama' => 'Bawang Putih',     'gram' => 20],
                ['nama' => 'Tomat',            'gram' => 60],
                ['nama' => 'Terasi',           'gram' => 6],
                ['nama' => 'Gula Merah',       'gram' => 15],
                ['nama' => 'Garam',            'gram' => 6],
                ['nama' => 'Minyak Goreng',    'gram' => 200],
                ['nama' => 'Kemangi',          'gram' => 15],
                ['nama' => 'Timun',            'gram' => 80],
                ['nama' => 'Kubis',            'gram' => 50],
            ],

            'Nasi Kuning' => [
                // resep untuk 4 porsi
                ['nama' => 'Beras',            'gram' => 400],
                ['nama' => 'Santan',           'gram' => 350],
                ['nama' => 'Kunyit',           'gram' => 20],   // dihaluskan
                ['nama' => 'Serai',            'gram' => 15],
                ['nama' => 'Daun Salam',       'gram' => 3],    // 2 lembar
                ['nama' => 'Daun Pandan',      'gram' => 5],
                ['nama' => 'Garam',            'gram' => 8],
                ['nama' => 'Bawang Goreng',    'gram' => 15],
            ],

            'Semur Daging' => [
                // resep untuk 4 porsi
                ['nama' => 'Daging Sapi',      'gram' => 600],
                ['nama' => 'Bawang Merah',     'gram' => 60],
                ['nama' => 'Bawang Putih',     'gram' => 30],
                ['nama' => 'Jahe',             'gram' => 15],
                ['nama' => 'Pala Bubuk',       'gram' => 3],
                ['nama' => 'Cengkeh',          'gram' => 4],    // 5 butir
                ['nama' => 'Kecap Manis',      'gram' => 80],   // dominan di semur
                ['nama' => 'Garam',            'gram' => 8],
                ['nama' => 'Gula Pasir',       'gram' => 10],
                ['nama' => 'Tomat',            'gram' => 60],
                ['nama' => 'Minyak Goreng',    'gram' => 25],
            ],

            'Lodeh Sayur' => [
                // resep untuk 4 porsi
                ['nama' => 'Labu Siam',        'gram' => 200],
                ['nama' => 'Kacang Panjang',   'gram' => 100],
                ['nama' => 'Tempe',            'gram' => 100],
                ['nama' => 'Santan',           'gram' => 500],
                ['nama' => 'Bawang Merah',     'gram' => 50],
                ['nama' => 'Bawang Putih',     'gram' => 25],
                ['nama' => 'Cabai Merah',      'gram' => 25],
                ['nama' => 'Kemiri',           'gram' => 15],
                ['nama' => 'Serai',            'gram' => 15],
                ['nama' => 'Daun Salam',       'gram' => 3],
                ['nama' => 'Lengkuas',         'gram' => 15],
                ['nama' => 'Gula Merah',       'gram' => 15],
                ['nama' => 'Garam',            'gram' => 8],
                ['nama' => 'Minyak Goreng',    'gram' => 20],
            ],

        ];

        foreach ($data as $title => $bahans) {
            $resep = Resep::where('title', $title)->first();

            if (! $resep) continue;

            foreach ($bahans as $item) {
                $bahan = Bahan::where('nama', $item['nama'])->first();

                if (! $bahan) continue;

                ResepBahan::updateOrCreate(
                    [
                        'resep_id' => $resep->id,
                        'bahan_id' => $bahan->id,
                    ],
                    [
                        'gram_total' => $item['gram'],
                    ]
                );
            }
        }
    }
}