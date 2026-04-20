<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_history';

    protected $fillable = [
        'user_id',
        'status',
        'purpose',
        'payment_token',
        'payment_method',
        'payment_total',
        'payment_date',
        'is_paid',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'is_paid' => 'boolean',
        'payment_total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}