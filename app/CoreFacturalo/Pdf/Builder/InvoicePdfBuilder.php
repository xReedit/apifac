<?php

namespace App\CoreFacturalo\Pdf\Builder;

class InvoicePdfBuilder extends PdfBuilder
{
    public function build($company, $document)
    {
        $template = 'simple_invoice';
        return $this->render($template, $company, $document);
    }
}