<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepAttachment extends Model
{
    use HasFactory;

    protected $table = 'resep_attachments';

    protected $fillable = [
        'resep_id',
        'mimetype',
        'path'
    ];


    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }
}