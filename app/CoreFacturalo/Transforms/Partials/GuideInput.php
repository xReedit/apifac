<?php

namespace App\CoreFacturalo\Transforms\Partials;

class GuideInput
{
    public static function transform($inputs)
    {
        $guides = null;
        if(key_exists('guias', $inputs)) {
            foreach ($inputs['guias'] as $row)
            {
                $number = $row['numero'];
                $document_type_id = $row['tipo_de_documento'];

                $guides[] = [
                    'number' => $number,
                    'document_type_id' => $document_type_id,
                ];
            }
        }

        return $guides;
    }
}