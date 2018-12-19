<?php

namespace App\Models;

use App\Models\Catalogs\ProcessType;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $with = ['user', 'soap_type', 'state_type', 'process_type'];

    protected $fillable = [
        'user_id',
        'soap_type_id',
        'state_type_id',
        'process_type_id',
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

    public function process_type()
    {
        return $this->belongsTo(ProcessType::class);
    }

    public function scopeWhereUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function getDownloadCdrAttribute()
    {
        return route('tenant.summaries.download', ['type' => 'cdr', 'id' => $this->id]);
    }

    public function getDownloadXmlAttribute()
    {
        return route('tenant.summaries.download', ['type' => 'xml', 'id' => $this->id]);
    }
}