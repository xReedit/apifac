<?php

namespace App\CoreFacturalo\Xml\Builder;

use App\CoreFacturalo\Interfaces\BuilderInterface;
use App\CoreFacturalo\Interfaces\DocumentInterface;

class NoteDebitXmlBuilder extends XmlBuilder implements BuilderInterface
{
    public function build(DocumentInterface $document)
    {
        $template = 'note_debit';
        return $this->render($template, $document);
    }
}