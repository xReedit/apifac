<?php

namespace App\CoreFacturalo\Transforms\Inputs;

use App\CoreFacturalo\Transforms\Functions;
use Exception;

class NoteInput
{
    public static function transform($inputs, $document)
    {
        $affected_document_series = $inputs['serie_de_documento_afectado'];
        $affected_document_number = $inputs['numero_de_documento_afectado'];
        $affected_document_type_id = $inputs['tipo_de_documento_afectado'];
        $note_credit_or_debit_type_id = $inputs['tipo_de_operacion'];
        $description = $inputs['motivo_o_sustento_de_la_nota'];

        if ($document['document_type_id'] === '07') {
            $note_type = 'credit';
            $note_credit_type_id = $note_credit_or_debit_type_id;
            $note_debit_type_id = null;
            $type = 'credit';
        } else {
            $note_type = 'debit';
            $note_credit_type_id = null;
            $note_debit_type_id = $note_credit_or_debit_type_id;
            $type = 'debit';
        }

        $affected_document = Functions::findDocument(compact('affected_document_type_id', 'affected_document_series', 'affected_document_number'));
        if($affected_document) {
            if($affected_document->state_type_id === '05') {
                return [
                    'type' => $type,
                    'group_id' => ($affected_document_type_id === '01')?'01':'02',
                    'document_base' => [
                        'note_type' => $note_type,
                        'note_credit_type_id' => $note_credit_type_id,
                        'note_debit_type_id' => $note_debit_type_id,
                        'description' => $description,
                        'affected_document_id' => $affected_document->id
                    ]
                ];
            } else {
                throw new Exception("El documento afectado {$affected_document->document_type->description} {$affected_document_series}-{$affected_document_number} tiene un estado {$affected_document->state_type->description}");
            }
        } else {
            throw new Exception("El documento afectado {$affected_document_series}-{$affected_document_number} no se encuentra registrado");
        }
    }
}