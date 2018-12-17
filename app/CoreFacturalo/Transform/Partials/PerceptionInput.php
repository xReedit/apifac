<?php

namespace App\CoreFacturalo\Transform\Partials;

class PerceptionInput
{
    public static function transform($data)
    {
        $code = $data['codigo'];
        $percentage = $data['porcentaje'];
        $amount = $data['monto'];
        $base = $data['base'];

        return [
            'code' => $code,
            'percentage' => $percentage,
            'amount' => $amount,
            'base' => $base,
        ];
    }
}