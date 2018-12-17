<?php

namespace App\CoreFacturalo\Transform\Partials;

class DetractionInput
{
    public static function transform($data)
    {
        $code = $data['codigo'];
        $percentage = $data['porcentaje'];
        $amount = $data['monto'];
        $payment_method_id = $data['codigo_metodo_pago'];
        $bank_account = $data['cuenta_bancaria'];

        return [
            'code' => $code,
            'percentage' => $percentage,
            'amount' => $amount,
            'payment_method_id' => $payment_method_id,
            'bank_account' => $bank_account,
        ];
    }
}