<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlannerDetail extends Model
{
    use HasFactory;

    protected $table = 'meal_planner_detail';

    protected $fillable = [
        'meal_planner_id',
        'resep_id',
        'meal_time',
    ];

    protected $casts = [
        'meal_planner_id' => 'integer',
        'resep_id' => 'integer',
    ];


    public function mealPlanner()
    {
        return $this->belongsTo(MealPlanner::class);
    }

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }
}