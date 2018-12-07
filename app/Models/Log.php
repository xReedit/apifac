<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'message',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}