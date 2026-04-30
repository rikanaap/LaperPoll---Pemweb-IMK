<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feedback;
use App\Models\Resep;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $feedbacks = [

            // Harmoni review Nasi Goreng
            [
                'resep_id' => 1,
                'user_id' => 3,
                'rating' => 5.0,
                'description' => 'Enak banget, gampang dibuat dan rasanya mantap.',
            ],

            // Ikbal review Capcay
            [
                'resep_id' => 5,
                'user_id' => 2,
                'rating' => 4.5,
                'description' => 'Sehat dan segar, cocok buat makan malam.',
            ],

            // Harmoni review Ayam Kecap
            [
                'resep_id' => 4,
                'user_id' => 3,
                'rating' => 4.8,
                'description' => 'Bumbu meresap dan pedasnya pas.',
            ],

            // Ikbal review Mie Goreng
            [
                'resep_id' => 2,
                'user_id' => 2,
                'rating' => 4.7,
                'description' => 'Simple dan cepat dimasak.',
            ],

            // Harmoni review Telur Dadar
            [
                'resep_id' => 3,
                'user_id' => 3,
                'rating' => 4.6,
                'description' => 'Renyah dan gurih, favorit sarapan.',
            ],
        ];

        foreach ($feedbacks as $item) {
            Feedback::create($item);
        }

        /*
        ======================================
        Update current_star di tabel reseps
        ======================================
        */

        $reseps = Resep::all();

        foreach ($reseps as $resep) {
            $avg = Feedback::where('resep_id', $resep->id)
                ->avg('rating');

            $resep->update([
                'current_star' => $avg ? round($avg, 1) : 0
            ]);
        }
    }
}
