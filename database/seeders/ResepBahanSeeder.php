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
        $data = [
            'Nasi Goreng Spesial' => [
                ['nama' => 'Nasi',         'gram' => 200],
                ['nama' => 'Telur',        'gram' => 60],
                ['nama' => 'Bawang Merah', 'gram' => 20],
                ['nama' => 'Bawang Putih', 'gram' => 15],
                ['nama' => 'Kecap Manis',  'gram' => 15],
            ],
            'Mie Goreng Jawa' => [
                ['nama' => 'Mie',          'gram' => 150],
                ['nama' => 'Telur',        'gram' => 60],
                ['nama' => 'Bawang Putih', 'gram' => 15],
                ['nama' => 'Kecap Manis',  'gram' => 10],
            ],
            'Telur Dadar Crispy' => [
                ['nama' => 'Telur', 'gram' => 120],
                ['nama' => 'Garam', 'gram' => 5],
            ],
            'Ayam Kecap Pedas' => [
                ['nama' => 'Ayam',         'gram' => 250],
                ['nama' => 'Kecap Manis',  'gram' => 20],
                ['nama' => 'Cabai Merah',  'gram' => 25],
                ['nama' => 'Bawang Putih', 'gram' => 15],
            ],
            'Capcay Sayur' => [
                ['nama' => 'Wortel',       'gram' => 80],
                ['nama' => 'Kubis',        'gram' => 100],
                ['nama' => 'Sawi',         'gram' => 80],
                ['nama' => 'Bawang Putih', 'gram' => 10],
            ],
            'Soto Ayam' => [
                ['nama' => 'Ayam',         'gram' => 300],
                ['nama' => 'Bawang Merah', 'gram' => 30],
                ['nama' => 'Bawang Putih', 'gram' => 20],
                ['nama' => 'Kunyit',       'gram' => 10],
                ['nama' => 'Jahe',         'gram' => 15],
                ['nama' => 'Serai',        'gram' => 20],
            ],
            'Rendang Daging' => [
                ['nama' => 'Daging Sapi',  'gram' => 500],
                ['nama' => 'Santan',       'gram' => 400],
                ['nama' => 'Cabai Merah',  'gram' => 50],
                ['nama' => 'Bawang Merah', 'gram' => 40],
                ['nama' => 'Bawang Putih', 'gram' => 20],
                ['nama' => 'Lengkuas',     'gram' => 20],
                ['nama' => 'Serai',        'gram' => 20],
            ],
            'Opor Ayam' => [
                ['nama' => 'Ayam',         'gram' => 400],
                ['nama' => 'Santan',       'gram' => 300],
                ['nama' => 'Bawang Merah', 'gram' => 30],
                ['nama' => 'Bawang Putih', 'gram' => 20],
                ['nama' => 'Kemiri',       'gram' => 20],
                ['nama' => 'Ketumbar',     'gram' => 5],
            ],
            'Nasi Uduk' => [
                ['nama' => 'Beras',        'gram' => 300],
                ['nama' => 'Santan',       'gram' => 200],
                ['nama' => 'Serai',        'gram' => 15],
                ['nama' => 'Daun Salam',   'gram' => 5],
                ['nama' => 'Garam',        'gram' => 5],
            ],
            'Gado-gado' => [
                ['nama' => 'Kentang',       'gram' => 150],
                ['nama' => 'Wortel',        'gram' => 100],
                ['nama' => 'Buncis',        'gram' => 80],
                ['nama' => 'Tahu',          'gram' => 100],
                ['nama' => 'Tempe',         'gram' => 100],
            ],
            'Pisang Goreng Crispy' => [
                ['nama' => 'Pisang',        'gram' => 200],
                ['nama' => 'Tepung Terigu', 'gram' => 100],
                ['nama' => 'Gula Pasir',    'gram' => 20],
                ['nama' => 'Garam',         'gram' => 3],
            ],
            'Tahu Crispy' => [
                ['nama' => 'Tahu',          'gram' => 200],
                ['nama' => 'Tepung Maizena','gram' => 50],
                ['nama' => 'Bawang Putih',  'gram' => 10],
                ['nama' => 'Garam',         'gram' => 5],
            ],
            'Tempe Mendoan' => [
                ['nama' => 'Tempe',         'gram' => 200],
                ['nama' => 'Tepung Terigu', 'gram' => 80],
                ['nama' => 'Bawang Putih',  'gram' => 10],
                ['nama' => 'Ketumbar',      'gram' => 3],
                ['nama' => 'Daun Bawang',   'gram' => 10],
            ],
            'Bakwan Sayur' => [
                ['nama' => 'Wortel',        'gram' => 80],
                ['nama' => 'Kubis',         'gram' => 80],
                ['nama' => 'Tepung Terigu', 'gram' => 100],
                ['nama' => 'Telur',         'gram' => 60],
                ['nama' => 'Daun Bawang',   'gram' => 10],
            ],
            'Onde-onde' => [
                ['nama' => 'Tepung Beras',  'gram' => 200],
                ['nama' => 'Gula Merah',    'gram' => 80],
                ['nama' => 'Kelapa',        'gram' => 50],
            ],
            'Es Teh Manis' => [
                ['nama' => 'Gula Pasir',    'gram' => 30],
            ],
            'Jus Alpukat' => [
                ['nama' => 'Alpukat',       'gram' => 200],
                ['nama' => 'Susu',          'gram' => 100],
                ['nama' => 'Gula Pasir',    'gram' => 20],
            ],
            'Es Cincau Hijau' => [
                ['nama' => 'Santan',        'gram' => 200],
                ['nama' => 'Gula Merah',    'gram' => 80],
                ['nama' => 'Garam',         'gram' => 3],
            ],
            'Klepon' => [
                ['nama' => 'Tepung Beras',  'gram' => 200],
                ['nama' => 'Gula Merah',    'gram' => 100],
                ['nama' => 'Kelapa',        'gram' => 80],
            ],
            'Bubur Sumsum' => [
                ['nama' => 'Tepung Beras',  'gram' => 150],
                ['nama' => 'Santan',        'gram' => 300],
                ['nama' => 'Gula Merah',    'gram' => 100],
                ['nama' => 'Garam',         'gram' => 3],
            ],
            'Puding Coklat' => [
                ['nama' => 'Susu',          'gram' => 400],
                ['nama' => 'Gula Pasir',    'gram' => 80],
            ],
            'Ayam Bakar Bumbu Rujak' => [
                ['nama' => 'Ayam',         'gram' => 400],
                ['nama' => 'Cabai Merah',  'gram' => 40],
                ['nama' => 'Bawang Merah', 'gram' => 30],
                ['nama' => 'Gula Merah',   'gram' => 30],
                ['nama' => 'Kemiri',       'gram' => 20],
            ],
            'Ikan Goreng Sambal' => [
                ['nama' => 'Ikan',         'gram' => 300],
                ['nama' => 'Cabai Merah',  'gram' => 30],
                ['nama' => 'Tomat',        'gram' => 50],
                ['nama' => 'Bawang Merah', 'gram' => 25],
                ['nama' => 'Kunyit',       'gram' => 5],
            ],
            'Tumis Kangkung' => [
                ['nama' => 'Kangkung',     'gram' => 200],
                ['nama' => 'Bawang Putih', 'gram' => 15],
                ['nama' => 'Cabai Rawit',  'gram' => 15],
                ['nama' => 'Garam',        'gram' => 5],
            ],
            'Sop Buntut' => [
                ['nama' => 'Wortel',       'gram' => 100],
                ['nama' => 'Kentang',      'gram' => 150],
                ['nama' => 'Bawang Merah', 'gram' => 30],
                ['nama' => 'Bawang Putih', 'gram' => 20],
                ['nama' => 'Jahe',         'gram' => 15],
                ['nama' => 'Seledri',      'gram' => 10],
            ],
            'Pecel Lele' => [
                ['nama' => 'Ikan',         'gram' => 300],
                ['nama' => 'Cabai Merah',  'gram' => 30],
                ['nama' => 'Bawang Merah', 'gram' => 20],
                ['nama' => 'Tomat',        'gram' => 40],
                ['nama' => 'Kunyit',       'gram' => 5],
            ],
            'Nasi Kuning' => [
                ['nama' => 'Beras',        'gram' => 300],
                ['nama' => 'Santan',       'gram' => 200],
                ['nama' => 'Kunyit',       'gram' => 10],
                ['nama' => 'Serai',        'gram' => 15],
                ['nama' => 'Daun Salam',   'gram' => 5],
            ],
            'Semur Daging' => [
                ['nama' => 'Daging Sapi',  'gram' => 400],
                ['nama' => 'Kecap Manis',  'gram' => 40],
                ['nama' => 'Bawang Merah', 'gram' => 30],
                ['nama' => 'Bawang Putih', 'gram' => 20],
                ['nama' => 'Jahe',         'gram' => 10],
            ],
            'Lodeh Sayur' => [
                ['nama' => 'Labu Siam',    'gram' => 150],
                ['nama' => 'Kacang Panjang','gram' => 100],
                ['nama' => 'Tempe',        'gram' => 100],
                ['nama' => 'Santan',       'gram' => 300],
                ['nama' => 'Bawang Merah', 'gram' => 25],
                ['nama' => 'Bawang Putih', 'gram' => 15],
            ],
        ];

        foreach ($data as $title => $bahans) {
            $resep = Resep::where('title', $title)->first();
            if (! $resep) continue;

            foreach ($bahans as $bahanItem) {
                $bahan = Bahan::where('nama', $bahanItem['nama'])->first();
                if (! $bahan) continue;

                ResepBahan::updateOrCreate(
                    ['resep_id' => $resep->id, 'bahan_id' => $bahan->id],
                    ['gram_total' => $bahanItem['gram']]
                );
            }
        }
    }
}