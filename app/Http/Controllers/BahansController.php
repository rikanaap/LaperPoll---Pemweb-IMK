<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    protected $fillable = [
        'nama',
        'expired_expectancy_day',
    ];

    public function userFridges()
    {
        return $this->hasMany(UserFridge::class);
    }
}