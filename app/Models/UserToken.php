<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    use HasFactory;

    protected $table = 'user_tokens';

    protected $fillable = [
        'identifier_id',
        'token_code',
        'payload',
        'user_id',
        'expired_date'
    ];

    protected $casts = [
        'expired_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}