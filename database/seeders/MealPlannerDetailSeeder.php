<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MealPlannerDetail;

class MealPlannerDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run(): void
    {
        $details = [

            /*
            Meal Planner ID 1 = Ikbal Hari Ini
            */
            [
                'meal_planner_id' => 1,
                'resep_id' => 1,
                'meal_time' => 'SA',
            ],
            [
                'meal_planner_id' => 1,
                'resep_id' => 2,
                'meal_time' => 'SI',
            ],
            [
                'meal_planner_id' => 1,
                'resep_id' => 3,
                'meal_time' => 'MA',
            ],

            /*
            Meal Planner ID 2 = Ikbal Besok
            */
            [
                'meal_planner_id' => 2,
                'resep_id' => 2,
                'meal_time' => 'SA',
            ],
            [
                'meal_planner_id' => 2,
                'resep_id' => 4,
                'meal_time' => 'SI',
            ],
            [
                'meal_planner_id' => 2,
                'resep_id' => 1,
                'meal_time' => 'MA',
            ],

            /*
            Meal Planner ID 3 = Harmoni Hari Ini
            */
            [
                'meal_planner_id' => 3,
                'resep_id' => 3,
                'meal_time' => 'SA',
            ],
            [
                'meal_planner_id' => 3,
                'resep_id' => 1,
                'meal_time' => 'SI',
            ],
            [
                'meal_planner_id' => 3,
                'resep_id' => 4,
                'meal_time' => 'MA',
            ],
        ];

        foreach ($details as $detail) {
            MealPlannerDetail::create($detail);
        }
    }
}
