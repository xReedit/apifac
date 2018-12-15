<?php

namespace App\Models;

use App\Models\System\SoapType;
use App\Models\System\StateType;
use Illuminate\Database\Eloquent\Model;

class Voided extends Model
{
    protected $table = 'voided';
    protected $with = ['user', 'soap_type', 'state_type'];

    protected $fillable = [
        'user_id',
        'soap_type_id',
        'state_type_id',
        'ubl_version',
        'date_of_issue',
        'date_of_reference',
        'identifier',
        'filename',
        'ticket',
        'has_ticket',
        'has_cdr',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
        'date_of_reference' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    public function state_type()
    {
        return $this->belongsTo(StateType::class);
    }

    public function scopeWhereUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function getDownloadCdrAttribute()
    {
        return route('tenant.voided.download', ['type' => 'cdr', 'id' => $this->id]);
    }

    public function getDownloadXmlAttribute()
    {
        return route('tenant.voided.download', ['type' => 'xml', 'id' => $this->id]);
    }
}