<?php

namespace App\CoreFacturalo\Transform\Partials;

class LegendInput
{
    public static function transform($data)
    {
        $array = [];
        foreach ($data as $row)
        {
            $code = $row['codigo'];
            $value = $row['valor'];

            $array[] = [
                'code' => $code,
                'value' => $value,
            ];
        }

        return $array;
    }
}