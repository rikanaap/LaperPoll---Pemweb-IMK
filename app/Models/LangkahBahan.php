<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LangkahBahan extends Model
{
    use HasFactory;

    protected $table = 'langkah_bahan';

    protected $fillable = [
        'langkah_id',
        'resep_bahan_id',
        'gram_total'
    ];



    public function langkah()
    {
        return $this->belongsTo(LangkahResep::class, 'langkah_id');
    }

    public function resepBahan()
    {
        return $this->belongsTo(ResepBahan::class, 'resep_bahan_id');
    }
}