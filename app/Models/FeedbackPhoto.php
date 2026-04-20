<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackPhoto extends Model
{
    use HasFactory;

    protected $table = 'feedback_photos';

    protected $fillable = [
        'feedback_id',
        'path'
    ];


    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}