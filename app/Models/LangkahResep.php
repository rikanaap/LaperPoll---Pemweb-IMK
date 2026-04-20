<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LangkahResep extends Model
{
    use HasFactory;

    protected $table = 'langkah_reseps';

    protected $fillable = [
        'resep_id',
        'step_order',
        'step_duration',
        'description'
    ];

    protected $casts = [
        'step_duration' => 'datetime:H:i:s',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }

    public function langkahBahans()
    {
        return $this->hasMany(LangkahBahan::class, 'langkah_id');
    }
}