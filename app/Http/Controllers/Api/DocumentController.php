<?php
namespace App\Http\Controllers\Api;

use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Controller;
use App\Models\Company;
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
        try {
            $facturalo = new Facturalo($request->all(), Company::byUser());

            DB::transaction(function () use($facturalo) {
                $facturalo->save();
                $facturalo->createXmlAndSign();
                $facturalo->createPdf();
            });

            $res = $facturalo->sendXml($facturalo->getXmlSigned());
            $document = $facturalo->getDocument();
            return [
                'success' => true,
                'data' => [
                    'number' => $document->number_full,
                    'filename' => $document->filename,
                    'external_id' => $document->external_id,
                    'number_to_letter' => $document->number_to_letter,
                    'hash' => $document->hash,
                    'qr' => $document->qr,
                ],
                'links' => [
                    'xml' => $document->download_external_xml,
                    'pdf' => $document->download_external_pdf,
                    'cdr' => $document->download_external_cdr,
                ],
                'response' => $res
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => "File: {$e->getFile()}, Line: {$e->getLine()}"
            ];
        }
    }
}