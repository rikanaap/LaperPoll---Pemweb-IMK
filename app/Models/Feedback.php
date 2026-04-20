<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'resep_id',
        'user_id',
        'rating',
        'description'
    ];

    protected $casts = [
        'rating' => 'float',
    ];


    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(FeedbackPhoto::class);
    }
}