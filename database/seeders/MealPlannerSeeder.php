<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MealPlanner;
use Carbon\Carbon;

class MealPlannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $planners = [

            // Ikbal
            [
                'user_id' => 2,
                'tanggal' => Carbon::today(),
                'max_calorie' => 1800,
            ],
            [
                'user_id' => 2,
                'tanggal' => Carbon::tomorrow(),
                'max_calorie' => 2000,
            ],

            // Harmoni
            [
                'user_id' => 3,
                'tanggal' => Carbon::today(),
                'max_calorie' => 1600,
            ],
            [
                'user_id' => 3,
                'tanggal' => Carbon::tomorrow(),
                'max_calorie' => 1700,
            ],
        ];

        foreach ($planners as $planner) {
            MealPlanner::create($planner);
        }
    }
}
