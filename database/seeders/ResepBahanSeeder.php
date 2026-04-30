<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResepBahan;

class ResepBahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $data = [

            /*
            ===================================
            RESEP ID 1 = Nasi Goreng Spesial
            ===================================
            */
            [
                'resep_id' => 1,
                'bahan_id' => 16, // Nasi
                'gram_total' => 200,
            ],
            [
                'resep_id' => 1,
                'bahan_id' => 1, // Telur
                'gram_total' => 60,
            ],
            [
                'resep_id' => 1,
                'bahan_id' => 6, // Bawang Merah
                'gram_total' => 20,
            ],
            [
                'resep_id' => 1,
                'bahan_id' => 7, // Bawang Putih
                'gram_total' => 15,
            ],
            [
                'resep_id' => 1,
                'bahan_id' => 25, // Kecap Manis
                'gram_total' => 15,
            ],

            /*
            ===================================
            RESEP ID 2 = Mie Goreng Jawa
            ===================================
            */
            [
                'resep_id' => 2,
                'bahan_id' => 17, // Mie
                'gram_total' => 150,
            ],
            [
                'resep_id' => 2,
                'bahan_id' => 1, // Telur
                'gram_total' => 60,
            ],
            [
                'resep_id' => 2,
                'bahan_id' => 7, // Bawang Putih
                'gram_total' => 15,
            ],
            [
                'resep_id' => 2,
                'bahan_id' => 25, // Kecap Manis
                'gram_total' => 10,
            ],

            /*
            ===================================
            RESEP ID 3 = Telur Dadar Crispy
            ===================================
            */
            [
                'resep_id' => 3,
                'bahan_id' => 1, // Telur
                'gram_total' => 120,
            ],
            [
                'resep_id' => 3,
                'bahan_id' => 24, // Garam
                'gram_total' => 5,
            ],

            /*
            ===================================
            RESEP ID 4 = Ayam Kecap Pedas
            ===================================
            */
            [
                'resep_id' => 4,
                'bahan_id' => 2, // Ayam
                'gram_total' => 250,
            ],
            [
                'resep_id' => 4,
                'bahan_id' => 25, // Kecap Manis
                'gram_total' => 20,
            ],
            [
                'resep_id' => 4,
                'bahan_id' => 8, // Cabai Merah
                'gram_total' => 25,
            ],
            [
                'resep_id' => 4,
                'bahan_id' => 7, // Bawang Putih
                'gram_total' => 15,
            ],

            /*
            ===================================
            RESEP ID 5 = Capcay Sayur
            ===================================
            */
            [
                'resep_id' => 5,
                'bahan_id' => 11, // Wortel
                'gram_total' => 80,
            ],
            [
                'resep_id' => 5,
                'bahan_id' => 13, // Kubis
                'gram_total' => 100,
            ],
            [
                'resep_id' => 5,
                'bahan_id' => 15, // Sawi
                'gram_total' => 80,
            ],
            [
                'resep_id' => 5,
                'bahan_id' => 7, // Bawang Putih
                'gram_total' => 10,
            ],

        ];

        foreach ($data as $item) {
            ResepBahan::create($item);
        }
    }
}
