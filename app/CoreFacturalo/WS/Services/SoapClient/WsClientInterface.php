<?php

namespace App\CoreFacturalo\WS\Services\SoapClient;

/**
 * Interface WsClientInterface.
 */
interface WsClientInterface
{
    /**
     * @param $function
     * @param $arguments
     *
     * @return mixed
     */
    public function call($function, $arguments);
}
