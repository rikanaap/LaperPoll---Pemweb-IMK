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
        'description',
        'cook_duration',
        'calorie',
        'current_star',
        'views_count',
        'thumbnail',
        'main_filter_id',
        'is_published',
    ];

    protected $casts = [
        'cook_duration' => 'string',
        'calorie' => 'integer',
        'current_star' => 'float',
        'views_count' => 'integer',
        'is_published' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // app/Models/Resep.php — tambahkan method ini

    public function getThumbnailUrlAttribute(): ?string
{
    if (! $this->thumbnail) return null;

    if (str_starts_with($this->thumbnail, 'images/')) {
        return asset('assets/' . $this->thumbnail);
    }

    return \Illuminate\Support\Facades\Storage::url($this->thumbnail);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mainFilter()
    {
        return $this->belongsTo(Filter::class, 'main_filter_id');
    }

    public function bahans()
    {
        return $this->belongsToMany(
            Bahan::class,
            'resep_bahan',
            'resep_id',
            'bahan_id'
        )->withPivot('gram_total');
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

    public function favoritedBy()
    {
    return $this->belongsToMany(User::class, 'favorites', 'resep_id', 'user_id');
    }

    public function filters()
    {
        return $this->belongsToMany(
            Filter::class,
            'resep_filters',
            'resep_id',
            'filters_id'
        );
    }

    public function mealPlannerDetails()
    {
        return $this->hasMany(MealPlannerDetail::class);
    }

    /**
     * Format cook_duration dari HH:MM:SS jadi "X jam Y menit" atau "Y menit"
     */
    public function getCookDurationFormattedAttribute(): string
    {
        $raw = $this->cook_duration;
        if (!$raw) return '-';

        $parts = explode(':', $raw);
        $hours   = isset($parts[0]) ? (int)$parts[0] : 0;
        $minutes = isset($parts[1]) ? (int)$parts[1] : 0;

        if ($hours > 0 && $minutes > 0) return "{$hours} jam {$minutes} menit";
        if ($hours > 0)                 return "{$hours} jam";
        if ($minutes > 0)               return "{$minutes} menit";
        return '< 1 menit';
    }
}