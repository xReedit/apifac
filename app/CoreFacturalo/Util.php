<?php

namespace App\CoreFacturalo;

use App\CoreFacturalo\Helpers\StorageDocument;
use App\CoreFacturalo\Pdf\Builder\InvoicePdfBuilder;
use App\CoreFacturalo\WS\Services\SunatEndpoints;
use App\CoreFacturalo\XmlDsig\Certificate\X509Certificate;
use App\CoreFacturalo\XmlDsig\Certificate\X509ContentType;
use App\Models\Company;
use App\Models\Document;
use Milon\Barcode\DNS2D;
use Barryvdh\DomPDF\Facade as PDF;

class Util
{
    use StorageDocument;

    public function getCpeBuilder($endpoint = SunatEndpoints::FE_BETA)
    {
        $path_certificate = __DIR__.DIRECTORY_SEPARATOR.'Certificates'.DIRECTORY_SEPARATOR.'demo.pem';
        $path_cache = storage_path('framework'.DIRECTORY_SEPARATOR.'cache');

        $cpeBuilder = new CpeBuilder();
        $cpeBuilder->setService($endpoint);
        $cpeBuilder->setCertificate(file_get_contents($path_certificate));
        $cpeBuilder->setCredentials('20000000000MODDATOS', 'moddatos');
        $cpeBuilder->setCachePath($path_cache);

        return $cpeBuilder;
    }

    public function getQr($document_id)
    {
        $document = Document::find($document_id);
        $company = Company::byUser();
        $customer = $document->customer;

        $arr = join('|', [
            $company->number,
            $document->document_type_id,
            $document->series,
            $document->number,
            $document->total_igv,
            $document->total,
            $document->date_of_issue->format('Y-m-d'),
            $customer->identity_document_type_id,
            $customer->number,
            $document->hash
        ]);

        return DNS2D::getBarcodePNG($arr, "QRCODE", 3 , 3);
    }

    public function createPdf($document_id)
    {
        $company = Company::byUser();
        $document = Document::find($document_id);
        $pdfBuilder = new InvoicePdfBuilder();
        $html = $pdfBuilder->build($company, $document);

        $pdf = PDF::loadHTML($html);

        $this->uploadStorage($company->number, 'pdf', $pdf->output(), $document->filename, 'pdf');
    }

    public static function generateCertificatePEM($content, $password)
    {
        $certificate = new X509Certificate($content, $password);
        return $certificate->export(X509ContentType::PEM);
    }
}