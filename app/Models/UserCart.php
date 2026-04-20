<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
    use HasFactory;

    protected $table = 'user_cart';

    protected $fillable = [
        'user_id',
        'bahan_id',
        'gram_total',
        'is_done'
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
}