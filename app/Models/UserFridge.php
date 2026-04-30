<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFridge extends Model
{
    use HasFactory;

    protected $table = 'user_fridge';

    protected $fillable = [
        'user_id',
        'bahan_id',
        'expired_date',
        'bought_date',
        'jumlah'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'bahan_id' => 'integer',
        'expired_date' => 'datetime',
        'bought_date' => 'datetime',
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