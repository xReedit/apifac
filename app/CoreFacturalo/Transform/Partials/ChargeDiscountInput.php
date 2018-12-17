<?php

namespace App\CoreFacturalo\Transform\Partials;

class ChargeDiscountInput
{
    public static function transform($data)
    {
        $array = [];
        foreach ($data as $row)
        {
            $code = $row['codigo'];
            $description = $row['descripcion'];
            $percentage = $row['porcentaje'];
            $amount = $row['monto'];
            $base = $row['base'];

            $array[] = [
                'code' => $code,
                'description' => $description,
                'percentage' => $percentage,
                'amount' => $amount,
                'base' => $base,
            ];
        }

        return $array;
    }
}