<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        01/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Model\Configuration;

/**
 * Class EndpointConfig
 * @package Divante\MagentoIntegrationBundle\Model\Configuration
 */
class EndpointConfig
{
    /** @var string */
    protected $payloadAttribute;
    /** @var string */
    protected $sendUrlParam;
    /** @var string */
    protected $deleteUrlparam;

    /**
     * @return string
     */
    public function getPayloadAttribute(): string
    {
        return $this->payloadAttribute;
    }

    /**
     * @param string $payloadAttribute
     */
    public function setPayloadAttribute(string $payloadAttribute): void
    {
        $this->payloadAttribute = $payloadAttribute;
    }

    /**
     * @return string
     */
    public function getSendUrlParam(): string
    {
        return $this->sendUrlParam;
    }

    /**
     * @param string $sendUrlParam
     */
    public function setSendUrlParam(string $sendUrlParam): void
    {
        $this->sendUrlParam = $sendUrlParam;
    }

    /**
     * @return string
     */
    public function getDeleteUrlparam(): string
    {
        return $this->deleteUrlparam;
    }

    /**
     * @param string $deleteUrlparam
     */
    public function setDeleteUrlparam(string $deleteUrlparam): void
    {
        $this->deleteUrlparam = $deleteUrlparam;
    }
}