<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoidedDocument extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'voided_id',
        'document_id',
    ];

    public function voided()
    {
        return $this->belongsTo(Voided::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}