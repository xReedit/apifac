<?php

namespace App\CoreFacturalo\Transforms\Inputs;

use App\CoreFacturalo\Transforms\Functions;
use App\CoreFacturalo\Transforms\Partials\ActionInput;
use App\CoreFacturalo\Transforms\Partials\ChargeInput;
use App\CoreFacturalo\Transforms\Partials\CustomerInput;
use App\CoreFacturalo\Transforms\Partials\DetractionInput;
use App\CoreFacturalo\Transforms\Partials\DiscountInput;
use App\CoreFacturalo\Transforms\Partials\EstablishmentInput;
use App\CoreFacturalo\Transforms\Partials\GuideInput;
use App\CoreFacturalo\Transforms\Partials\ItemInput;
use App\CoreFacturalo\Transforms\Partials\LegendInput;
use App\CoreFacturalo\Transforms\Partials\OptionalInput;
use App\CoreFacturalo\Transforms\Partials\PerceptionInput;
use App\CoreFacturalo\Transforms\Partials\PrepaymentInput;
use App\CoreFacturalo\Transforms\Partials\RelatedInput;
use Exception;
use Illuminate\Support\Str;

class DocumentInput
{
    public static function transform($inputs)
    {
        $soap_type_id = Functions::soapTypeId();
        $document_type_id = $inputs['codigo_tipo_documento'];
        $series = $inputs['serie_documento'];
        $number = $inputs['numero_documento'];

        $date_of_issue = $inputs['fecha_de_emision'];
        $time_of_issue = $inputs['hora_de_emision'];
        $currency_type_id = $inputs['codigo_tipo_moneda'];
        $purchase_order = array_key_exists('numero_orden_de_compra', $inputs)?$inputs['numero_orden_de_compra']:null;

        if(!in_array($document_type_id, ['01', '03', '07', '08'])) {
            throw new Exception("El código tipo de documento {$document_type_id} es incorrecto.");
        }

        $totals = $inputs['totales'];
        $total_prepayment = array_key_exists('total_anticipos', $totals)?$totals['total_anticipos']:0;
        $total_discount = array_key_exists('total_descuentos', $totals)?$totals['total_descuentos']:0;
        $total_charge = array_key_exists('total_cargos', $totals)?$totals['total_cargos']:0;
        $total_exportation = array_key_exists('total_exportacion', $totals)?$totals['total_exportacion']:0;
        $total_free = array_key_exists('total_operaciones_gratuitas', $totals)?$totals['total_operaciones_gratuitas']:0;
        $total_taxed = array_key_exists('total_operaciones_gravadas', $totals)?$totals['total_operaciones_gravadas']:0;
        $total_unaffected = array_key_exists('total_operaciones_inafectas', $totals)?$totals['total_operaciones_inafectas']:0;
        $total_exonerated = array_key_exists('total_operaciones_exoneradas', $totals)?$totals['total_operaciones_exoneradas']:0;
        $total_igv = array_key_exists('total_igv', $totals)?$totals['total_igv']:0;
        $total_base_isc = array_key_exists('total_base_isc', $totals)?$totals['total_base_isc']:0;
        $total_isc = array_key_exists('total_isc', $totals)?$totals['total_isc']:0;
        $total_base_other_taxes = array_key_exists('total_base_otros_impuestos', $totals)?$totals['total_base_otros_impuestos']:0;
        $total_other_taxes = array_key_exists('total_otros_impuestos', $totals)?$totals['total_otros_impuestos']:0;
        $total_taxes = array_key_exists('total_impuestos', $totals)?$totals['total_impuestos']:0;
        $total_value = array_key_exists('total_valor', $totals)?$totals['total_valor']:0;
        $total = $totals['total_venta'];

        $number = Functions::newNumber(compact('soap_type_id', 'document_type_id', 'series', 'number'));
        Functions::validateUniqueDocument(compact('soap_type_id', 'document_type_id', 'series', 'number'));
        $filename = Functions::filename(compact('document_type_id', 'series', 'number'));

        return [
            'actions' => ActionInput::transform($inputs),
            'document' => [
                'user_id' => auth()->id(),
                'establishment' => EstablishmentInput::transform($inputs),
                'external_id' => Str::uuid(),
                'soap_type_id' => $soap_type_id,
                'state_type_id' => '01',
                'ubl_version' => '2.1',
                'filename' => $filename,
                'group_id' => null,
                'document_type_id' => $document_type_id,
                'series' => $series,
                'number' => $number,
                'date_of_issue' => $date_of_issue,
                'time_of_issue' => $time_of_issue,
                'customer' => CustomerInput::transform($inputs),
                'currency_type_id' => $currency_type_id,
                'purchase_order' => $purchase_order,
                'total_prepayment' => $total_prepayment,
                'total_discount' => $total_discount,
                'total_charge' => $total_charge,
                'total_exportation' => $total_exportation,
                'total_free' => $total_free,
                'total_taxed' => $total_taxed,
                'total_unaffected' => $total_unaffected,
                'total_exonerated' => $total_exonerated,
                'total_igv' => $total_igv,
                'total_base_isc' => $total_base_isc,
                'total_isc' => $total_isc,
                'total_base_other_taxes' => $total_base_other_taxes,
                'total_other_taxes' => $total_other_taxes,
                'total_taxes' => $total_taxes,
                'total_value' => $total_value,
                'total' => $total,
                'charges' => ChargeInput::transform($inputs),
                'discounts' => DiscountInput::transform($inputs),
                'prepayments' => PrepaymentInput::transform($inputs),
                'guides' => GuideInput::transform($inputs),
                'related' => RelatedInput::transform($inputs),
                'perception' => PerceptionInput::transform($inputs),
                'detraction' => DetractionInput::transform($inputs),
                'legends' => LegendInput::transform($inputs),
                'extras' => OptionalInput::transform($inputs),
                'items' => ItemInput::transform($inputs)
            ]
        ];
    }
}