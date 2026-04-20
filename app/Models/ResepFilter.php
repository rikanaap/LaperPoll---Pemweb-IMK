<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepFilter extends Model
{
    use HasFactory;

    protected $table = 'resep_filters';

    protected $fillable = [
        'resep_id',
        'filters_id'
    ];


    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }

    public function filter()
    {
        return $this->belongsTo(Filter::class, 'filters_id');
    }
}