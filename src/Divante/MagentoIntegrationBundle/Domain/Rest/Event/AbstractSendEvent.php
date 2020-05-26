<?php

namespace Divante\MagentoIntegrationBundle\Domain\Rest\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractSendEvent
 * @package Divante\MagentoIntegrationBundle\Domain\Rest\Event
 */
abstract class AbstractSendEvent
{
    /** @var RequestInterface|ResponseInterface */
    private $data;

    /**
     * AbstractSendEvent constructor.
     * @param RequestInterface|ResponseInterface $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return RequestInterface|ResponseInterface
     */
    public function getData()
    {
        return $this->data;
    }
}
