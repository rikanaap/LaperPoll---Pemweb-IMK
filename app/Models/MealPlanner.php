<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlanner extends Model
{
    use HasFactory;

    protected $table = 'meal_planner';

    protected $fillable = [
        'user_id',
        'tanggal',
        'max_calorie',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'tanggal' => 'date',
        'max_calorie' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(MealPlannerDetail::class);
    }
}