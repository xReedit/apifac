<?php

namespace App\CoreFacturalo\Transform\Partials;

class RelatedDocumentInput
{
    public static function transform($data)
    {
        $array = [];
        foreach ($data as $row)
        {
            $number = $row['numero'];
            $document_type_id = $row['tipo_de_documento'];
            $amount = $row['monto'];

            $array[] = [
                'number' => $number,
                'document_type_id' => $document_type_id,
                'amount' => $amount
            ];
        }

        return $array;
    }
}