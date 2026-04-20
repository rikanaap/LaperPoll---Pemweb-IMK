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