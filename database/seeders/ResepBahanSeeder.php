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

            /*
            ===================================
            NASI GORENG SPESIAL
            ===================================
            */
            [
                'resep' => 'Nasi Goreng Spesial',
                'bahans' => [
                    ['nama' => 'Nasi', 'gram' => 200],
                    ['nama' => 'Telur', 'gram' => 60],
                    ['nama' => 'Bawang Merah', 'gram' => 20],
                    ['nama' => 'Bawang Putih', 'gram' => 15],
                    ['nama' => 'Kecap Manis', 'gram' => 15],
                ]
            ],

            /*
            ===================================
            MIE GORENG JAWA
            ===================================
            */
            [
                'resep' => 'Mie Goreng Jawa',
                'bahans' => [
                    ['nama' => 'Mie', 'gram' => 150],
                    ['nama' => 'Telur', 'gram' => 60],
                    ['nama' => 'Bawang Putih', 'gram' => 15],
                    ['nama' => 'Kecap Manis', 'gram' => 10],
                ]
            ],

            /*
            ===================================
            TELUR DADAR CRISPY
            ===================================
            */
            [
                'resep' => 'Telur Dadar Crispy',
                'bahans' => [
                    ['nama' => 'Telur', 'gram' => 120],
                    ['nama' => 'Garam', 'gram' => 5],
                ]
            ],

            /*
            ===================================
            AYAM KECAP PEDAS
            ===================================
            */
            [
                'resep' => 'Ayam Kecap Pedas',
                'bahans' => [
                    ['nama' => 'Ayam', 'gram' => 250],
                    ['nama' => 'Kecap Manis', 'gram' => 20],
                    ['nama' => 'Cabai Merah', 'gram' => 25],
                    ['nama' => 'Bawang Putih', 'gram' => 15],
                ]
            ],

            /*
            ===================================
            CAPCAY SAYUR
            ===================================
            */
            [
                'resep' => 'Capcay Sayur',
                'bahans' => [
                    ['nama' => 'Wortel', 'gram' => 80],
                    ['nama' => 'Kubis', 'gram' => 100],
                    ['nama' => 'Sawi', 'gram' => 80],
                    ['nama' => 'Bawang Putih', 'gram' => 10],
                ]
            ],
        ];

        foreach ($data as $item) {

            $resep = Resep::where('title', $item['resep'])->first();

            if (!$resep) {
                continue;
            }

            foreach ($item['bahans'] as $bahanItem) {

                $bahan = Bahan::where('nama', $bahanItem['nama'])->first();

                if (!$bahan) {
                    continue;
                }

                ResepBahan::updateOrCreate(
                    [
                        'resep_id' => $resep->id,
                        'bahan_id' => $bahan->id,
                    ],
                    [
                        'gram_total' => $bahanItem['gram'],
                    ]
                );
            }
        }
    }
}