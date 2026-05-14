<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resep;

class ResepSeeder extends Seeder
{
    public function run(): void
    {
        $reseps = [

            [
                'user_id' => 1,
                'title' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng rumahan dengan telur dan ayam.',
                'cook_duration' => '00:20:00',
                'calorie' => 550,
                'current_star' => 4.8,
                'views_count' => 120,

                // STORAGE PATH
                'thumbnail' => 'reseps/nasi_goreng.jpeg',

                'main_filter_id' => 1,
                'is_published' => true,
            ],

            [
                'user_id' => 1,
                'title' => 'Mie Goreng Jawa',
                'description' => 'Mie goreng manis gurih khas Jawa.',
                'cook_duration' => '00:15:00',
                'calorie' => 480,
                'current_star' => 4.6,
                'views_count' => 95,

                // STORAGE PATH
                'thumbnail' => 'reseps/mie-goreng-jawa.jpg',

                'main_filter_id' => 1,
                'is_published' => true,
            ],

            [
                'user_id' => 2,
                'title' => 'Telur Dadar Crispy',
                'description' => 'Telur dadar renyah dan gurih.',
                'cook_duration' => '00:10:00',
                'calorie' => 300,
                'current_star' => 4.5,
                'views_count' => 75,

                // STORAGE PATH
                'thumbnail' => 'reseps/telur-dadar-crispy.jpg',

                'main_filter_id' => 2,
                'is_published' => true,
            ],

            [
                'user_id' => 2,
                'title' => 'Ayam Kecap Pedas',
                'description' => 'Ayam manis pedas dengan bumbu kecap.',
                'cook_duration' => '00:30:00',
                'calorie' => 620,
                'current_star' => 4.9,
                'views_count' => 210,

                // STORAGE PATH
                'thumbnail' => 'reseps/ayam-kecap-pedas.jpg',

                'main_filter_id' => 1,
                'is_published' => true,
            ],

            [
                'user_id' => 3,
                'title' => 'Capcay Sayur',
                'description' => 'Menu sehat dengan berbagai sayuran.',
                'cook_duration' => '00:25:00',
                'calorie' => 350,
                'current_star' => 4.7,
                'views_count' => 80,

                // STORAGE PATH
                'thumbnail' => 'reseps/capcay-sayur.jpg',

                'main_filter_id' => 1,
                'is_published' => true,
            ],

        ];

        foreach ($reseps as $resep) {

            Resep::updateOrCreate(

                [
                    'title' => $resep['title']
                ],

                $resep

            );

        }
    }
}