<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'cook_duration',
        'calorie',
        'current_star',
        'views_count'
    ];

    protected $casts = [
        'cook_duration' => 'datetime:H:i:s',
        'current_star' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'resep_bahan')
                    ->withPivot('gram_total');
    }

    public function langkahs()
    {
        return $this->hasMany(LangkahResep::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function attachments()
    {
        return $this->hasMany(ResepAttachment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function filters()
    {
        return $this->belongsToMany(Filter::class, 'resep_filters', 'resep_id', 'filters_id');
    }

    public function mealPlannerDetails()
    {
        return $this->hasMany(MealPlannerDetail::class);
    }
}