<?php

namespace App\CoreFacturalo;

use App\CoreFacturalo\Documents\InvoiceBuilder;
use App\CoreFacturalo\Documents\NoteCreditBuilder;
use App\CoreFacturalo\Documents\NoteDebitBuilder;
use App\CoreFacturalo\Documents\SummaryBuilder;
use App\CoreFacturalo\Documents\VoidedBuilder;
use App\CoreFacturalo\Helpers\Xml\XmlFormat;
use App\CoreFacturalo\Helpers\Xml\XmlHash;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\WS\Client\WsClient;
use App\CoreFacturalo\WS\Services\BillSender;
use App\CoreFacturalo\WS\Services\SummarySender;
use App\CoreFacturalo\WS\Services\SunatEndpoints;
use App\CoreFacturalo\WS\Signed\XmlSigned;
use Exception;
use Mpdf\Mpdf;

class Facturalo
{
    use StorageDocument;

    protected $signer;
    protected $wsClient;
    protected $inputs;
    protected $company;
    protected $document;
    protected $type;
    protected $xmlSigned;
    protected $pathCertificate;
    protected $soapUsername;
    protected $soapPassword;
    protected $endpoint;

    public function __construct($inputs, $company)
    {
        $this->signer = new XmlSigned();
        $this->wsClient = new WsClient();
        $this->inputs = $inputs;
        $this->company = $company;
        $this->type = $inputs['type'];
        $this->dataSoapType();
    }

    public function createXmlAndSign()
    {
        $xmlUnsigned = $this->createXml();
        $xmlSigned = $this->signXml($xmlUnsigned);
        $this->updateDocument($xmlSigned);
    }

    public function setDocument($document)
    {
        $this->document = $document;
    }

    public function save()
    {
        switch ($this->type) {
            case 'debit':
                $builder = new NoteDebitBuilder();
                break;
            case 'credit':
                $builder = new NoteCreditBuilder();
                break;
            case 'summary':
                $builder = new SummaryBuilder();
                break;
            case 'voided':
                $builder = new VoidedBuilder();
                break;
            default:
                $builder = new InvoiceBuilder();
                break;
        }

        $this->document = $builder->save($this->inputs);
    }

    private function dataSoapType()
    {
        if($this->company->soap_type_id === '01') {
            $this->soapUsername = '20000000000MODDATOS';
            $this->soapPassword = 'moddatos';
            $this->pathCertificate = __DIR__.DIRECTORY_SEPARATOR.'WS'.DIRECTORY_SEPARATOR.'Signed'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'certificate.pem';
            $this->endpoint = SunatEndpoints::FE_BETA;
        } else {
            $this->soapUsername = $this->company->soap_username;
            $this->soapPassword = $this->company->soap_password;
            $this->pathCertificate = storage_path('app'.DIRECTORY_SEPARATOR.'certificates'.$this->company->certificate);
            $this->endpoint = SunatEndpoints::FE_PRODUCCION;
        }
    }

    private function updateDocument($xmlContent)
    {
        if(!in_array($this->type, ['summary', 'voided'])) {
            $hash = $this->getHash($xmlContent);
            $this->document->update([
                'hash' => $hash,
                'qr' => $this->getQr($hash)
            ]);
        }
    }

    private function getHash($content)
    {
        $helper = new XmlHash();
        return $helper->getHashSign($content);
    }

    private function getQr($hash)
    {
        $customer = $this->document->customer;
        $text = join('|', [
            $this->company->number,
            $this->document->document_type_id,
            $this->document->series,
            $this->document->number,
            $this->document->total_igv,
            $this->document->total,
            $this->document->date_of_issue->format('Y-m-d'),
            $customer->identity_document_type_id,
            $customer->number,
            $hash
        ]);

        $temp = tempnam(sys_get_temp_dir(), 'qrCode_');
        $qrCode = new  \Mpdf\QrCode\QrCode($text);
        $qrCode->displayPNG(120, [255, 255, 255], [0, 0, 0], $temp);
        return base64_encode(file_get_contents($temp));
    }

    public function createXml()
    {
        $template = new Template();
        $xmlUnsigned = XmlFormat::format($template->xml($this->type, $this->company, $this->document));
        $this->uploadFile($xmlUnsigned, 'unsigned');
        return $xmlUnsigned;
    }

    public function signXml($content)
    {
        $this->signer->setCertificateFromFile($this->pathCertificate);
        $xmlSigned = $this->signer->signXml($content);
        $this->xmlSigned = $xmlSigned;
        $this->uploadFile($xmlSigned, 'signed');
        return $xmlSigned;
    }

    public function sendXml($content)
    {
        $this->wsClient->setCredentials($this->soapUsername, $this->soapPassword);
        $this->wsClient->setService($this->endpoint);

        $sender = in_array($this->type, ['summary', 'voided'])?new SummarySender():new BillSender();
        $sender->setClient($this->wsClient);

        $res = $sender->send($this->document->filename, $content);

        if(!$res->isSuccess()) {
            throw new Exception("Code: {$res->getError()->getCode()}; Description: {$res->getError()->getMessage()}");
        } else {
            $cdrResponse = $res->getCdrResponse();
            $this->uploadFile($res->getCdrZip(), 'cdr');
            return [
                'code' => $cdrResponse->getCode(),
                'description' => $cdrResponse->getDescription(),
                'notes' => $cdrResponse->getNotes()
            ];
        }
    }

    public function createPdf()
    {
        $template = new Template();
        $html = $template->pdf($this->type, $this->company, $this->document);

        $pdf = new Mpdf();
        $pdf->WriteHTML($html);
        $this->uploadFile($pdf->output('', 'S'), 'pdf');
    }

    public function uploadFile($file_content, $file_type)
    {
        $this->uploadStorage($this->document->filename, $file_content, $file_type, $this->company->number);
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function getXmlSigned()
    {
        return $this->xmlSigned;
    }
}
