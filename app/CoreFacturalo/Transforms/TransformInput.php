<?php

namespace App\CoreFacturalo\Transform;

use App\CoreFacturalo\Transforms\Inputs\DocumentInput;
use App\CoreFacturalo\Transforms\Inputs\InvoiceInput;
use App\CoreFacturalo\Transforms\Inputs\NoteInput;
use Closure;
use Exception;

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
        $originalAttributes = $this->originalAttribute($request->all());
        $request->replace($originalAttributes);
        return $next($request);
    }

    private function originalAttribute($inputs)
    {
        try {
            $aux_document = DocumentInput::transform($inputs);
            $document = $aux_document['document'];
            if (in_array($document['document_type_id'], ['01', '03'])) {
                $aux_document_base = InvoiceInput::transform($inputs, $document);
            } else {
                $aux_document_base = NoteInput::transform($inputs, $document);
            }
            $document['group_id'] = $aux_document_base['group_id'];

            $original_attributes = [
                'type' => $aux_document_base['type'],
                'document' => $document,
                'document_base' => $aux_document_base['document_base'],
                'actions' => $aux_document['actions'],
                'success' => true,
            ];

            return $original_attributes;

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => "File: {$e->getFile()}, Line: {$e->getLine()}"
            ];
        }
    }
}