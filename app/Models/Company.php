<?php

namespace App\Models;

use App\Models\Catalogs\Code;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'identity_document_type_id',
        'number',
        'name',
        'trade_name',
        'soap_type_id',
        'soap_username',
        'soap_password',
        'certificate',
        'logo',
    ];

    public function identity_document_type()
    {
        return $this->belongsTo(Code::class, 'identity_document_type_id');
    }

    public static function byUser()
    {
        return Company::where('user_id', auth()->id())->first();
    }
}