<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepBahan extends Model
{
    use HasFactory;

    protected $table = 'resep_bahan';

    protected $fillable = [
        'resep_id',
        'bahan_id',
        'gram_total'
    ];


    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }

    public function langkahBahans()
    {
        return $this->hasMany(LangkahBahan::class, 'resep_bahan_id');
    }
}