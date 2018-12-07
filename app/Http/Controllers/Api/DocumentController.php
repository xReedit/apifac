<?php
namespace App\Http\Controllers\Api;

use App\Core\Builder\Documents\InvoiceBuilder;
use App\Core\Builder\Documents\NoteCreditBuilder;
use App\Core\Builder\Documents\NoteDebitBuilder;
use App\Core\Builder\XmlBuilder;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('transform.input', ['only' => ['store']]);
    }

    public function store(Request $request)
    {
        $document_type_code = ($request->has('document'))?$request->input('document.document_type_code'):
                                                          $request->input('document_type_code');

        $document = DB::transaction(function () use($request, $document_type_code) {
            switch ($document_type_code) {
                case '01':
                case '03':
                    $builder = new InvoiceBuilder();
                    break;
                case '07':
                    $builder = new NoteCreditBuilder();
                    break;
                case '08':
                    $builder = new NoteDebitBuilder();
                    break;
                default:
                    throw new Exception('Tipo de documento ingresado es inválido');
            }

            $builder->save($request->all());
            $xmlBuilder = new XmlBuilder();
            $xmlBuilder->createXMLSigned($builder);
            $document = $builder->getDocument();

            return $document;
        });

        return [
            'success' => true,
            'data' => [
                'id' => $document->id,
                'number' => $document->number_full,
                'hash' => $document->hash,
                'qr' => $document->qr,
                'filename' => $document->filename,
                'external_id' => $document->external_id,
                'number_to_letter' => $document->number_to_letter,
                'link_xml' => $document->download_external_xml,
                'link_pdf' => $document->download_external_pdf,
                'link_cdr' => $document->download_external_cdr,
            ]
        ];
    }
}