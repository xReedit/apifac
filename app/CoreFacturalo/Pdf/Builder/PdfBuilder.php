<?php

namespace App\CoreFacturalo\Pdf\Builder;

class PdfBuilder
{
    public function render($template, $company, $document)
    {
        view()->addLocation(__DIR__.'/../Templates');
        $view = view($template, compact('company', 'document'))->render();

        file_put_contents(public_path('demo.html'), $view);

        return $view;
    }
}