<?php

namespace App\CoreFacturalo\Transforms;

use App\Models\Document;
use Exception;

class Functions
{
    public static function validateUniqueDocument($data)
    {
        $document = self::findDocument($data);
        if($document) {
            throw new Exception("El documento: {$data['document_type_id']} {$data['series']}-{$data['number']}, ya se encuentra registrado, tiene un estado {$document->state_type->description}");
        }
    }

    public static function newNumber($data)
    {
        $number = $data['number'];
        if ($number === '#') {
            $document = self::lastDocument($data);
            $number = ($document)?(int)$document->number+1:1;
        }
        return $number;
    }

    public static function lastDocument($data)
    {
        return Document::lastDocument($data['soap_type_id'], $data['document_type_id'], $data['series']);
    }

    public static function findDocument($data)
    {
        return Document::findDocument($data['soap_type_id'], $data['document_type_id'], $data['series'], $data['number']);
    }

    public static function soapTypeId()
    {
        return auth()->user()->company->soap_type_id;
    }

    public static function filename($data)
    {
        $company =auth()->user()->company;
        return join('-', [$company->number, $data['document_type_id'], $data['series'], $data['number']]);
    }
}