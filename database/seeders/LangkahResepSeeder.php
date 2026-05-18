<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LangkahResep;

class LangkahResepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $langkahs = [

            // RESEP ID 1 = Nasi Goreng Spesial
            [
                'resep_id' => 1,
                'step_order' => 1,
                'description' => 'Panaskan minyak di wajan.',
            ],
            [
                'resep_id' => 1,
                'step_order' => 2,
                'description' => 'Tumis bawang merah dan bawang putih hingga harum.',
            ],
            [
                'resep_id' => 1,
                'step_order' => 3,
                'description' => 'Masukkan telur lalu orak-arik.',
            ],
            [
                'resep_id' => 1,
                'step_order' => 4,
                'description' => 'Masukkan nasi dan kecap, aduk rata.',
            ],
            [
                'resep_id' => 1,
                'step_order' => 5,
                'description' => 'Sajikan selagi hangat.',
            ],

            // RESEP ID 2 = Mie Goreng Jawa
            [
                'resep_id' => 2,
                'step_order' => 1,
                
                'description' => 'Rebus mie hingga matang lalu tiriskan.',
            ],
            [
                'resep_id' => 2,
                'step_order' => 2,
                'description' => 'Tumis bawang hingga harum.',
            ],
            [
                'resep_id' => 2,
                'step_order' => 3,
                'description' => 'Masukkan mie dan bumbu, aduk rata.',
            ],

            // RESEP ID 3 = Telur Dadar Crispy
            [
                'resep_id' => 3,
                'step_order' => 1,
             
                'description' => 'Kocok telur dengan garam.',
            ],
            [
                'resep_id' => 3,
                'step_order' => 2,
                'description' => 'Goreng telur tipis hingga renyah.',
            ],

            // RESEP ID 4 = Ayam Kecap Pedas
            [
                'resep_id' => 4,
                'step_order' => 1,
             
                'description' => 'Bersihkan ayam dengan mencuci dan berikan perasan jeruk nipis sebelum dibilas.',
            ],
            [
                'resep_id' => 4,
                'step_order' => 2,
                'description' => 'Haluskan bawang merah, bawang putih, dan cabai, tumis bumbu halus hingga harum.',
            ],
            [
                'resep_id' => 4,
                'step_order' => 3,
             
                'description' => 'Setelah itu, masukkan ayam yang telah digoreng ke dalam tumisan bumbu. 
                Tambahkan air secukupnya hingga ayam terendam.',
            ],
            [
                'resep_id' => 4,
                'step_order' => 4,
                'description' => 'Masak dengan api kecil untuk memastikan ayam meresap matang.
                Sertakan kecap, gula merah, garam, dan gula sesuai selera',
            ],
            [
                'resep_id' => 4,
                'step_order' => 5,
             
                'description' => 'Tambahkan tomat dan daun bawang untuk sentuhan segar',
            ],
            [
                'resep_id' => 4,
                'step_order' => 6,
                'description' => 'Sajikan dengan siap.',
            ],

        ];

        foreach ($langkahs as $langkah) {
            LangkahResep::create($langkah);
        }
    }
}
