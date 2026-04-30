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
                'step_duration' => '00:03:00',
                'description' => 'Panaskan minyak di wajan.',
            ],
            [
                'resep_id' => 1,
                'step_order' => 2,
                'step_duration' => '00:05:00',
                'description' => 'Tumis bawang merah dan bawang putih hingga harum.',
            ],
            [
                'resep_id' => 1,
                'step_order' => 3,
                'step_duration' => '00:03:00',
                'description' => 'Masukkan telur lalu orak-arik.',
            ],
            [
                'resep_id' => 1,
                'step_order' => 4,
                'step_duration' => '00:07:00',
                'description' => 'Masukkan nasi dan kecap, aduk rata.',
            ],
            [
                'resep_id' => 1,
                'step_order' => 5,
                'step_duration' => '00:02:00',
                'description' => 'Sajikan selagi hangat.',
            ],

            // RESEP ID 2 = Mie Goreng Jawa
            [
                'resep_id' => 2,
                'step_order' => 1,
                'step_duration' => '00:05:00',
                'description' => 'Rebus mie hingga matang lalu tiriskan.',
            ],
            [
                'resep_id' => 2,
                'step_order' => 2,
                'step_duration' => '00:04:00',
                'description' => 'Tumis bawang hingga harum.',
            ],
            [
                'resep_id' => 2,
                'step_order' => 3,
                'step_duration' => '00:06:00',
                'description' => 'Masukkan mie dan bumbu, aduk rata.',
            ],

            // RESEP ID 3 = Telur Dadar Crispy
            [
                'resep_id' => 3,
                'step_order' => 1,
                'step_duration' => '00:02:00',
                'description' => 'Kocok telur dengan garam.',
            ],
            [
                'resep_id' => 3,
                'step_order' => 2,
                'step_duration' => '00:05:00',
                'description' => 'Goreng telur tipis hingga renyah.',
            ],

        ];

        foreach ($langkahs as $langkah) {
            LangkahResep::create($langkah);
        }
    }
}
