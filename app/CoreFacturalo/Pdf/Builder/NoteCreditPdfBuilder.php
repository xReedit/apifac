<?php

namespace App\CoreFacturalo\Pdf\Builder;

class NoteCreditPdfBuilder extends PdfBuilder
{
    public function build($company, $document)
    {
        $template = 'simple_note_credit';
        return $this->render($template, $company, $document);
    }
}