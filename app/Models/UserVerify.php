<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerify extends Model
{
    use HasFactory;

    protected $table = 'user_verify';

    protected $fillable = [
        'user_id',
        'payment_token',
        'expired_date',
        'is_active'
    ];

    protected $casts = [
        'expired_date' => 'datetime',
        'is_active' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}