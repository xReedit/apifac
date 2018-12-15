<?php

namespace App\Models;

use App\Models\Catalogs\Code;
use App\Models\Catalogs\CurrencyType;
use App\Models\Catalogs\DocumentType;
use App\Models\System\Group;
use App\Models\System\SoapType;
use App\Models\System\StateType;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $with = ['user', 'soap_type', 'state_type', 'group', 'document_type',
                       'currency_type', 'details', 'invoice', 'note'];

    protected $fillable = [
        'user_id',
        'establishment',
        'external_id',
        'soap_type_id',
        'state_type_id',
        'ubl_version',
        'group_id',
        'document_type_id',
        'series',
        'number',
        'date_of_issue',
        'time_of_issue',
        'customer',
        'currency_type_id',
        'total_other_charges',
        'total_exportation',
        'total_taxed',
        'total_unaffected',
        'total_exonerated',
        'total_igv',
        'total_base_isc',
        'total_isc',
        'total_base_other_taxes',
        'total_other_taxes',
        'total_taxes',
        'total',
        'purchase_order',

        'legends',
        'guides',
        'related_documents',
        'optional',

        'filename',
        'hash',
        'qr',

        'has_xml',
        'has_pdf',
        'has_cdr'
    ];

    protected $casts = [
        'date_of_issue' => 'date',
    ];

    public function getCustomerAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setCustomerAttribute($value)
    {
        $this->attributes['customer'] = (is_null($value))?null:json_encode($value);
    }

    public function getEstablishmentAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setEstablishmentAttribute($value)
    {
        $this->attributes['establishment'] = (is_null($value))?null:json_encode($value);
    }

    public function getGuidesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setGuidesAttribute($value)
    {
        $this->attributes['guides'] = (is_null($value))?null:json_encode($value);
    }

    public function getRelatedDocumentsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setRelatedDocumentsAttribute($value)
    {
        $this->attributes['related_documents'] = (is_null($value))?null:json_encode($value);
    }

    public function getLegendsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setLegendsAttribute($value)
    {
        $this->attributes['legends'] = (is_null($value))?null:json_encode($value);
    }

    public function getOptionalAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setOptionalAttribute($value)
    {
        $this->attributes['optional'] = (is_null($value))?null:json_encode($value);
    }

    public function getNumberFullAttribute()
    {
        return $this->series.'-'.$this->number;
    }

    public function getNumberToLetterAttribute()
    {
        $legends = $this->legends;
        $legend = collect($legends)->where('code', '1000')->first();
        return $legend->value;
    }

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

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function document_type()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function note()
    {
        return $this->hasOne(Note::class);
    }

    public function details()
    {
        return $this->hasMany(Detail::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function scopeWhereUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function getVoidedAttribute()
    {
        if ($this->group_id === '01') {
            return Voided::whereHas('documents', function ($query) {
                                $query->where('document_id', $this->id);
                            })
                            ->whereIn('state_type_id', ['03', '05'])
                            ->first();
        }

        return Summary::whereHas('documents', function ($query) {
                            $query->where('document_id', $this->id);
                        })
                        ->whereIn('state_type_id', ['03', '05'])
                        ->where('process_type_id', 3)
                        ->first();
    }

    public function scopeWhereProcessType($query, $process_type_id)
    {
        if($process_type_id === 1) {
            return $query->where('state_type_id', '01');
        }
        return $query->where('state_type_id', '13');
    }

    public function getDownloadXmlAttribute()
    {
        return route('documents.download', ['type' => 'xml', 'document' => $this->id]);
    }

    public function getDownloadPdfAttribute()
    {
        return route('documents.download', ['type' => 'pdf', 'document' => $this->id]);
    }

    public function getDownloadCdrAttribute()
    {
        return route('documents.download', ['type' => 'cdr', 'document' => $this->id]);
    }

    public function getDownloadExternalXmlAttribute()
    {
        return route('documents.download_external', ['type' => 'xml', 'external_id' => $this->external_id]);
    }

    public function getDownloadExternalPdfAttribute()
    {
        return route('documents.download_external', ['type' => 'pdf', 'external_id' => $this->external_id]);
    }

    public function getDownloadExternalCdrAttribute()
    {
        return route('documents.download_external', ['type' => 'cdr', 'external_id' => $this->external_id]);
    }

    public function getDocumentTypeDescriptionAttribute()
    {
        $document_type = Code::byCatalogAndCode('01', $this->document_type_code);
        return $document_type->description;
    }
}