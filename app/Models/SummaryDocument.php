<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryDocument extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'summary_id',
        'document_id',
    ];

    public function summary()
    {
        return $this->belongsTo(Summary::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}