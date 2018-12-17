<?php

namespace App\CoreFacturalo\Transform\Partials;

class GuideInput
{
    public static function transform($data)
    {
        $array = [];
        foreach ($data as $row)
        {
            $number = $row['numero'];
            $document_type_id = $row['tipo_de_documento'];

            $array[] = [
                'number' => $number,
                'document_type_id' => $document_type_id,
            ];
        }

        return $array;
    }
}