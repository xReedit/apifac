<?php
namespace App\Http\Controllers\Api;

use App\CoreFacturalo\Documents\InvoiceBuilder;
use App\CoreFacturalo\Documents\NoteCreditBuilder;
use App\CoreFacturalo\Helpers\HashXml;
use App\CoreFacturalo\Helpers\StorageDocument;
use App\CoreFacturalo\Util;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    use StorageDocument;

    public function __construct()
    {
        $this->middleware('transform.input', ['only' => ['store']]);
    }

    public function store(Request $request)
    {
        $document_type_id = ($request->has('document'))?$request->input('document.document_type_id'):
                                                          $request->input('document_type_code');

        DB::beginTransaction();
        try {
            if($document_type_id === '08') {
//                $builder = new NoteDebitBuilder();
            } elseif ($document_type_id === '07') {
                $builder = new NoteCreditBuilder();
            } else {
                $builder = new InvoiceBuilder();
            }

            $builder->save($request->all());
            $document = $builder->getDocument();

            $util = new Util();
            $cpeUtil = $util->getCpeBuilder();
            $xmlSigned = $cpeUtil->getXmlSigned($builder);

            $company = Company::byUser();
            $this->uploadStorage($company->number, 'signed', $xmlSigned, $document->filename);

            $res = $cpeUtil->sendXml(get_class($builder), $builder->getDocument()->filename, $xmlSigned);

            if(!$res->isSuccess()) {
                throw new Exception("Code: {$res->getError()->getCode()}; Description: {$res->getError()->getMessage()}");
            } else {
                $hashXml = new HashXml();
                $document->hash = $hashXml->getHashSign($xmlSigned);
                $document->qr = $util->getQr($document->id);
                $document->save();

                $this->uploadStorage($company->number, 'cdr', $res->getCdrZip(), 'R-'.$document->filename, 'zip');

                $util->createPdf($document->id);

                $actions = $request->input('actions');
                $send_email = false;
                if($actions['send_email']) {
                    $send_email = $this->email($document->id);
                }
                DB::commit();
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
                    ],
                    'send_email' => $send_email,
                ];
            }
        } catch (Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => "File: {$e->getFile()}, Line: {$e->getLine()}"
            ];
        }

//
//        $document = DB::transaction(function () use($request, $document_type_code) {
//            switch ($document_type_code) {
//                case '01':
//                case '03':
//                    $builder = new InvoiceBuilder();
//                    break;
//                case '07':
//                    $builder = new NoteCreditBuilder();
//                    break;
//                case '08':
//                    $builder = new NoteDebitBuilder();
//                    break;
////                default:
////                    throw new Exception('Tipo de documento ingresado es inválido');
//            }
//
//            $builder->save($request->all());
//            $xmlBuilder = new XmlBuilder();
//            $xmlBuilder->createXMLSigned($builder);
//            $document = $builder->getDocument();
//
//            return $document;
//        });
//
//        return [
//            'success' => true,
//            'data' => [
//                'id' => $document->id,
//                'number' => $document->number_full,
//                'hash' => $document->hash,
//                'qr' => $document->qr,
//                'filename' => $document->filename,
//                'external_id' => $document->external_id,
//                'number_to_letter' => $document->number_to_letter,
//                'link_xml' => $document->download_external_xml,
//                'link_pdf' => $document->download_external_pdf,
//                'link_cdr' => $document->download_external_cdr,
//            ]
//        ];
    }
}