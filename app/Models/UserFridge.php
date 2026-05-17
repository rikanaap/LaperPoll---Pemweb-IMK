<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFridge extends Model
{
    // Nama tabel di DB adalah "user_fridge" (bukan "user_fridges")
    protected $table = 'user_fridge';

    protected $fillable = [
        'user_id',
        'bahan_id',
        'jumlah',
        'bought_date',
        'expired_date',
    ];

    protected $casts = [
        'bought_date'  => 'date',
        'expired_date' => 'date',
    ];

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}