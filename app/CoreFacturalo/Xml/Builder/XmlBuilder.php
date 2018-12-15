<?php

namespace App\CoreFacturalo\Xml\Builder;

class XmlBuilder
{
    public function render($template, $doc)
    {
        view()->addLocation(__DIR__.'/../Templates');
        $view = view($template, [
            'company' => $doc->getCompany(),
            'document' => $doc->getDocument()
        ])->render();

        return $view;
    }
}