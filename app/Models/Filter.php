<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'level',
        'description'
    ];

    // ──────────────────────────────────────────────────────────
    // Static helpers
    // ──────────────────────────────────────────────────────────

    public static function levelLabel(?int $level): string
    {
        if (is_null($level)) return '—';

        return match($level) {
            1       => 'Jenis Makanan',
            2       => 'Metode Masak',
            3       => 'Rasa / Preferensi',
            default => "Level {$level}",
        };
    }

    public static function levelColor(?int $level): string
    {
        return match($level) {
            1       => 'badge--blue',
            2       => 'badge--orange',
            3       => 'badge--green',
            default => 'badge--gray',
        };
    }

    // ──────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────

    public function reseps()
    {
        return $this->belongsToMany(Resep::class, 'resep_filters', 'filters_id', 'resep_id');
    }

    public function resepFilters()
    {
        return $this->hasMany(ResepFilter::class, 'filters_id');
    }

    public function mainReseps()
    {
        return $this->hasMany(Resep::class, 'main_filter_id');
    }
}