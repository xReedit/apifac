<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->replace($this->originalAttribute($request->all()));
        return $next($request);
    }

    private function originalAttribute($inputs)
    {
        $items = [];
        foreach ($inputs['items'] as $row)
        {
            $attributes = [];
            if(array_key_exists('datos_adicionales', $row)) {
                foreach ($row['datos_adicionales'] as $add)
                {
                    $attributes[] = [
                        'code' => $add['codigo'],
                        'name' => $add['nombre'],
                        'value' => array_key_exists('valor', $add)?$add['valor']:null,
                        'start_date' => array_key_exists('fecha_inicio', $add)?$add['fecha_inicio']:null,
                        'end_date' => array_key_exists('fecha_fin', $add)?$add['fecha_fin']:null,
                        'duration' => array_key_exists('duracion', $add)?$add['duracion']:null,
                    ];
                }
            }

            $charges = [];
            if(array_key_exists('cargos', $row)) {
                foreach ($row['cargos'] as $add)
                {
                    $charges[] = [
                        'code' => $add['codigo'],
                        'name' => $add['nombre'],
                        'value' => array_key_exists('valor', $add)?$add['valor']:null,
                        'start_date' => array_key_exists('fecha_inicio', $add)?$add['fecha_inicio']:null,
                        'end_date' => array_key_exists('fecha_fin', $add)?$add['fecha_fin']:null,
                        'duration' => array_key_exists('duracion', $add)?$add['duracion']:null,
                    ];
                }
            }

            $discounts = [];
            if(array_key_exists('descuentos', $row)) {
                foreach ($row['descuentos'] as $add)
                {
                    $discounts[] = [
                        'code' => $add['codigo'],
                        'name' => $add['nombre'],
                        'value' => array_key_exists('valor', $add)?$add['valor']:null,
                        'start_date' => array_key_exists('fecha_inicio', $add)?$add['fecha_inicio']:null,
                        'end_date' => array_key_exists('fecha_fin', $add)?$add['fecha_fin']:null,
                        'duration' => array_key_exists('duracion', $add)?$add['duracion']:null,
                    ];
                }
            }

            $items[] = [
                'internal_id' => array_key_exists('codigo_interno', $row)?$row['codigo_interno']:null,
                'item_description' => $row['descripcion'],
                'item_code' => array_key_exists('codigo_producto_de_sunat', $row)?$row['codigo_producto_sunat']:null,
                'item_code_gs1' => array_key_exists('codigo_producto_gsl', $row)?$row['codigo_producto_gsl']:null,
                'unit_type_code' => $row['unidad_de_medida'],
                'quantity' => $row['cantidad'],
                'unit_value' => $row['valor_unitario'],

                'affectation_igv_type_code' => $row['codigo_tipo_afectacion_igv'],
                'total_base_igv' => $row['total_base_igv'],
                'percentage_igv' => $row['porcentaje_de_igv'],
                'total_igv' => $row['total_igv'],

                'system_isc_type_code' => array_key_exists('codigo_tipo_sistema_isc', $row)?$row['codigo_tipo_sistema_isc']:null,
                'total_base_isc' => array_key_exists('total_base_isc', $row)?$row['total_base_isc']:0,
                'percentage_isc' => array_key_exists('porcentaje_de_isc', $row)?$row['porcentaje_de_isc']:0,
                'total_isc' => array_key_exists('total_isc', $row)?$row['total_isc']:0,

                'total_base_other_taxes' => array_key_exists('total_base_otros_impuestos', $row)?$row['total_base_otros_impuestos']:0,
                'percentage_other_taxes' => array_key_exists('percentage_other_taxes', $row)?$row['percentage_other_taxes']:0,
                'total_other_taxes' => array_key_exists('total_otros_impuestos', $row)?$row['total_otros_impuestos']:0,

                'total_taxes' => $row['total_impuestos'],

                'price_type_code' => $row['codigo_tipo_precio'],
                'unit_price' => $row['precio_unitario'],

                'total_value' => $row['valor_de_venta_por_item'],
                'total' => $row['total_por_item'],

                'attributes' => $attributes,
                'charges' => $charges,
                'discounts' => $discounts
            ];
        }

        $prepayments = null;
//        if(array_key_exists('informacion_adicional_anticipos', $inputs)) {
//                $serie_number = explode('-',$inputs['informacion_adicional_anticipos']['informacion_prepagado_o_anticipado']['serie_y_numero_de_documento_que_se_realizo_el_anticipo']);
//                $prepayments[] = [
//                    'series' => $serie_number[0],
//                    'number' => $serie_number[1],
//                    'document_type_code' => $inputs['informacion_adicional_anticipos']['informacion_prepagado_o_anticipado']['tipo_de_comprobante_que_se_realizo_el_anticipo'],
//                    'currency_type_code' => $inputs['informacion_adicional_anticipos']['informacion_prepagado_o_anticipado']['tipo_de_documento_del_emisor_del_anticipo'],
//                    'total' => array_key_exists('total_anticipos', $inputs['informacion_adicional_anticipos'])?$inputs['informacion_adicional_anticipos']['total_anticipos']:0,
//                ];
//        }
//
        $additional_documents = null;
//        if(array_key_exists('DocumentosAdicionalesRelacionados', $inputs)) {
//            foreach ($inputs['DocumentosAdicionalesRelacionados'] as $row)
//            {
//                $additional_documents[] = [
//                    'number' => $row['NumeroDocumento'],
//                    'document_type_code' => $row['CodigoTipoDocumento'],
//                ];
//            }
//        }

        $perception = null;
//        if(array_key_exists('informacion_adicional_percepciones', $inputs)) {
//            $perception = [
//                'account' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['codigo_de_tipo_de_monto'],
//                'reception_type_code' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['codigo_de_regimen_de_percepcion'],
//                'base' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['base_imponible_percepcion'],
//                'total_perception' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['monto_de_la_percepcion'],
//                'total' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['monto_total_incluido_la_percepcion'],
//            ];
//        }
//
        $detraction = null;
//        if(array_key_exists('Detraccion', $inputs)) {
//            $detraction = [
//                'account' => $inputs['Detraccion']['CuentaBancoNacion'],
//                'code' => $inputs['Detraccion']['CodigoBienServicio'],
//                'percentage' => $inputs['Detraccion']['PorcentajeDetraccion'],
//                'total' => $inputs['Detraccion']['TotalDetraccion'],
//            ];
//        }

        $optional = [];
        if(array_key_exists('extras', $inputs)) {
            $optional = [
                'observations' => array_key_exists('observaciones', $inputs['extras'])?$inputs['extras']['observaciones']:null,
                'method_payment' => array_key_exists('forma_de_pago', $inputs['extras'])?$inputs['extras']['forma_de_pago']:null,
                'salesman' => array_key_exists('vendedor', $inputs['extras'])?$inputs['extras']['vendedor']:null,
                'box_number' => array_key_exists('caja', $inputs['extras'])?$inputs['extras']['caja']:null ,
                'format_pdf' => array_key_exists('formato_pdf', $inputs['extras'])?$inputs['extras']['formato_pdf']:'a4'
            ];
        }

        $document_base = [];
        $group_id = null;
        /*
         * Invoice
         */
        if (in_array($inputs['tipo_de_documento'], ['01', '03'])) {
            $document_base = [
                'operation_type_code' => $inputs['tipo_de_operacion'],
                'total_free' => array_key_exists('total_operaciones_gratuitas', $inputs['totales'])?$inputs['totales']['total_operaciones_gratuitas']:0,
                'total_global_discount' => array_key_exists('total_descuento_global', $inputs['totales'])?$inputs['totales']['total_descuento_global']:0,
                'total_discount' => array_key_exists('total_descuentos', $inputs['totales'])?$inputs['totales']['total_descuentos']:0,
                'total_charge' => array_key_exists('total_cargos', $inputs['totales'])?$inputs['totales']['total_cargos']:0,
                'date_of_due' => array_key_exists('fecha_de_vencimiento', $inputs)?$inputs['fecha_de_vencimiento']:null,
                'base_global_discount' => array_key_exists('base_descuento_global', $inputs['totales'])?$inputs['totales']['base_descuento_global']:0,
                'percentage_global_discount' => array_key_exists('porcentaje_descuento_global', $inputs['totales'])?$inputs['totales']['porcentaje_descuento_global']:0,
                'total_prepayment' => array_key_exists('total_anticipos', $inputs['totales'])?$inputs['totales']['total_anticipos']:0,
                'purchase_order' => array_key_exists('numero_de_orden_de_compra', $inputs)?$inputs['numero_de_orden_de_compra']:null,
                'detraction' => $detraction,
                'perception' => $perception,
                'prepayments' => $prepayments,
            ];
            $group_id = ($inputs['tipo_de_documento'] === '01')?'01':'02';
        }

        /*
         * Note Credit, Note Debit
         */
        if (in_array($inputs['tipo_de_documento'], ['07', '08'])) {
            $affected_document_series_and_number = explode('-',$inputs['serie_y_numero_de_documento_afectado']);
            $document_base = [
                'note_type_code' => $inputs['codigo_de_tipo_de_la_nota'],
                'description' => $inputs['motivo_o_sustento_de_la_nota'],
                'affected_document_type_code' => $inputs['tipo_de_documento_afectado'],
                'affected_document_series' => $affected_document_series_and_number[0],
                'affected_document_number' => $affected_document_series_and_number[1],
                'total_global_discount' => array_key_exists('total_descuento_global', $inputs['totales'])?$inputs['totales']['total_descuento_global']:0,
                'total_prepayment' => array_key_exists('total_anticipos', $inputs['totales'])?$inputs['totales']['total_anticipos']:0,
            ];
            $group_id = ($inputs['tipo_de_documento_afectado'] === '01')?'01':'02';
        }

        $guides = [];
        if (array_key_exists('guias', $inputs)) {
            foreach ($inputs['guias'] as $row)
            {
                $guides[] = [
                    'number' => $row['numero'],
                    'document_type_code' => $row['tipo_de_documento'],
                ];
            }
        }

        $legends = [];
        if (array_key_exists('leyendas', $inputs['informacion_adicional'])) {
            foreach ($inputs['informacion_adicional']['leyendas'] as $row)
            {
                $legends[] = [
                    'code' => $row['codigo_de_la_leyenda'],
                    'description' => $row['descripcion_de_la_leyenda'],
                ];
            }
        }

        $company = Company::byUser();
        $series_and_number = explode('-',$inputs['serie_y_numero_correlativo']);
        $total_exportation  = array_key_exists('total_exportacion', $inputs['totales'])?$inputs['totales']['total_exportacion']:0;
        $total_taxed = array_key_exists('total_operaciones_gravadas', $inputs['totales'])?$inputs['totales']['total_operaciones_gravadas']:0;
        $total_unaffected = array_key_exists('total_operaciones_inafectas', $inputs['totales'])?$inputs['totales']['total_operaciones_inafectas']:0;
        $total_exonerated = array_key_exists('total_operaciones_exoneradas', $inputs['totales'])?$inputs['totales']['total_operaciones_exoneradas']:0;


        /*
         * Establishment
         */
        $establishment = null;
        if (array_key_exists('datos_del_emisor', $inputs)) {
            $data = $inputs['datos_del_emisor'];
            if (array_key_exists('ubigeo', $data)) {
                $department_id = substr($data['ubigeo'], 0 ,2);
                $province_id = substr($data['ubigeo'], 0 ,4);
                $district_id = $data['ubigeo'];
                $establishment = [
                    '$department_id' => $department_id,
                    '$province_id' => $province_id,
                    '$district_id' => $district_id,
                    'address' => $data['direccion'],
                    'email' => $data['direccion'],
                    'phone' => $data['email'],
                    'code' => $data['codigo_del_domicilio_fiscal']
                ];
            } else {

            }
        } else {

        }

        /*
         * Customer
         */
        $customer = null;
        if (array_key_exists('datos_del_receptor', $inputs)) {
            $data = $inputs['datos_del_receptor'];
            if (array_key_exists('ubigeo', $data)) {
                $department_id = substr($data['ubigeo'], 0 ,2);
                $province_id = substr($data['ubigeo'], 0 ,4);
                $district_id = $data['ubigeo'];
                $customer = [
                    'department_id' => $department_id,
                    'province_id' => $province_id,
                    'district_id' => $district_id,
                    'address' => $data['direccion'],
                    'email' => $data['direccion'],
                    'phone' => $data['email'],
                ];
            } else {

            }
        } else {

        }

        $original_attributes = [
            'document' => [
                'user_id' => auth()->id(),
                'external_id' => '',
                'state_type_id' => '01',
                'ubl_version' => $inputs['version_del_ubl'],
                'soap_type_id' => $company->soap_type_id,
                'group_id' => $group_id,
                'document_type_code' => $inputs['tipo_de_documento'],
                'date_of_issue' => $inputs['fecha_de_emision'],
                'time_of_issue' => $inputs['hora_de_emision'],
                'series' => $series_and_number[0],
                'number' => $series_and_number[1],
                'currency_type_code' => $inputs['tipo_de_moneda'],
                'total_exportation' => $total_exportation,
                'total_taxed' => $total_taxed,
                'total_unaffected' => $total_unaffected,
                'total_exonerated' => $total_exonerated,
                'total_igv' => array_key_exists('sumatoria_igv', $inputs['totales'])?$inputs['totales']['sumatoria_igv']:0,
                'total_isc' => array_key_exists('sumatoria_isc', $inputs['totales'])?$inputs['totales']['sumatoria_isc']:0,
                'total_other_taxes' => array_key_exists('sumatoria_otros_tributos', $inputs['totales'])?$inputs['totales']['sumatoria_otros_tributos']:0,
                'total_other_charges' => array_key_exists('sumatoria_otros_cargos', $inputs['totales'])?$inputs['totales']['sumatoria_otros_cargos']:0,
                'total_discount' => array_key_exists('total_descuentos', $inputs['totales'])?$inputs['totales']['total_descuentos']:0,
                'total_value' => $total_exportation + $total_taxed + $total_unaffected + $total_exonerated,
                'total' => $inputs['totales']['total_de_la_venta'],
                'establishment' => $establishment,
                'customer_id' => $customer,
                'items' => $items,
                'guides' => $guides,
                'additional_documents' => $additional_documents,
                'legends' => $legends,
                'filename' => '',
                'hash' => '',
                'qr' => '',
                'optional' => $optional,
            ],
            'document_base' => $document_base
        ];

        return $original_attributes;
    }
}