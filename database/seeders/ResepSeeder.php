<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'thumbnail' => 'nasi-goreng.jpg',
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
                'thumbnail' => 'mie-goreng.jpg',
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
                'thumbnail' => 'telur-dadar.jpg',
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
                'thumbnail' => 'ayam-kecap.jpg',
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
                'thumbnail' => 'capcay.jpg',
                'main_filter_id' => 1,
                'is_published' => true,
            ],

        ];

        foreach ($reseps as $resep) {
            Resep::create($resep);
        }
    }
}