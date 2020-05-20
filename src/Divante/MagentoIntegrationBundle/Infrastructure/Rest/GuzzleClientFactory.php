<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/**
 * Class GuzzleClientFactory
 * @package Divante\MagentoIntegrationBundle\Infrastructure\Rest
 */
class GuzzleClientFactory
{
    /**
     * @param $container
     * @return Client
     */
    public function getGuzzleClient(&$container)
    {
        $history = Middleware::history($container);
        $stack = HandlerStack::create();
        $stack->push($history);
        return new Client(['handler' => $stack]);
    }
}
