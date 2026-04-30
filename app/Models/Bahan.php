<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'expired_expectancy_day'
    ];


    public function reseps()
    {
        return $this->belongsToMany(Resep::class, 'resep_bahan')
                    ->withPivot('gram_total');
    }

    public function resepBahans()
    {
        return $this->hasMany(ResepBahan::class);
    }

    public function userFridges()
    {
        return $this->hasMany(UserFridge::class);
    }

    public function userCarts()
    {
        return $this->hasMany(UserCart::class);
    }
}