<?php

namespace App\CoreFacturalo\Interfaces;

use App\CoreFacturalo\WS\Response\BaseResult;

/**
 * Interface SenderInterface.
 */
interface SenderInterface
{
    /**
     * Send document.
     *
     * @param string $filename Filename
     * @param string $content Content File
     *
     * @return BaseResult
     */
    public function send($filename, $content);
}
