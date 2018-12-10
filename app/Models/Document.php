<?php

namespace App\Models;

use App\Models\Catalogs\Code;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $with = ['details', 'invoice', 'note'];

    protected $fillable = [
        'user_id',
        'external_id',
        'soap_type_id',
        'state_type_id',
        'ubl_version',
        'group_id',
        'document_type_code',
        'date_of_issue',
        'time_of_issue',
        'series',
        'number',
        'currency_type_code',
        'total_exportation',
        'total_taxed',
        'total_unaffected',
        'total_exonerated',
        'total_igv',
        'total_isc',
        'total_other_taxes',
        'total_other_charges',
        'total_discount',
        'total_value',
        'total',
        'establishment',
        'customer',
        'guides',
        'related_documents',
        'legends',
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
        return (object) json_decode($value);
    }

    public function setCustomerAttribute($value)
    {
        $this->attributes['guides'] = json_encode($value);
    }

    public function getEstablishmentAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setEstablishmentAttribute($value)
    {
        $this->attributes['guides'] = json_encode($value);
    }

    public function getGuidesAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setGuidesAttribute($value)
    {
        $this->attributes['guides'] = json_encode($value);
    }

    public function getRelatedDocumentsAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setRelatedDocumentsAttribute($value)
    {
        $this->attributes['related_documents'] = json_encode($value);
    }

    public function getLegendsAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setLegendsAttribute($value)
    {
        $this->attributes['legends'] = json_encode($value);
    }

    public function getOptionalAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setOptionalAttribute($value)
    {
        $this->attributes['optional'] = json_encode($value);
    }

    public function getNumberFullAttribute()
    {
        return $this->series.'-'.$this->number;
    }

    public function getNumberToLetterAttribute()
    {
        $legends = $this->legends;
        $legend = collect($legends)->where('code', '1000')->first();
        return $legend->description;
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
        return route('tenant.documents.download', ['type' => 'xml', 'document' => $this->id]);
    }

    public function getDownloadPdfAttribute()
    {
        return route('tenant.documents.download', ['type' => 'pdf', 'document' => $this->id]);
    }

    public function getDownloadCdrAttribute()
    {
        return route('tenant.documents.download', ['type' => 'cdr', 'document' => $this->id]);
    }

    public function getDownloadExternalXmlAttribute()
    {
        return route('tenant.documents.download_external', ['type' => 'xml', 'external_id' => $this->external_id]);
    }

    public function getDownloadExternalPdfAttribute()
    {
        return route('tenant.documents.download_external', ['type' => 'pdf', 'external_id' => $this->external_id]);
    }

    public function getDownloadExternalCdrAttribute()
    {
        return route('tenant.documents.download_external', ['type' => 'cdr', 'external_id' => $this->external_id]);
    }

    public function getDocumentTypeDescriptionAttribute()
    {
        $document_type = Code::byCatalogAndCode('01', $this->document_type_code);
        return $document_type->description;
    }
}