<?php

namespace App\CoreFacturalo\Xml\Builder;

use App\CoreFacturalo\Interfaces\BuilderInterface;
use App\CoreFacturalo\Interfaces\DocumentInterface;

class InvoiceXmlBuilder extends XmlBuilder implements BuilderInterface
{
    public function build(DocumentInterface $document)
    {
        $template = 'invoice';
        return $this->render($template, $document);
    }
}