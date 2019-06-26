<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        01/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Provider;

use Divante\MagentoIntegrationBundle\Model\Configuration\EndpointConfig;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class RestOutputProvider
 * @package Divante\MagentoIntegrationBundle\Provider
 */
abstract class RestOutputProvider implements RestOutputProviderInterface
{
    const CONFIG_PATH = '';

    /**
     * @var array
     */
    protected $config;

    /** @var FileLocatorInterface */
    protected $fileLocator;

    /**
     * MagentoRestOutputProvider constructor.
     * @param FileLocatorInterface $fileLocator
     */
    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
        $this->parseConfig();
    }


    public function parseConfig(): void
    {
        $this->config = Yaml::parseFile($this->fileLocator->locate(static::CONFIG_PATH));
    }

    /**
     * @return EndpointConfig
     */
    public function getAssetConfig(): EndpointConfig
    {
        return $this->getConfig('asset');
    }

    /**
     * @return EndpointConfig
     */
    public function getProductConfig(): EndpointConfig
    {
        return $this->getConfig('product');
    }

    /**
     * @return EndpointConfig
     */
    public function getCategoryConfig(): EndpointConfig
    {
        return $this->getConfig('category');
    }

    /**
     * @param $nodeName
     * @return EndpointConfig
     */
    protected function getConfig($nodeName): EndpointConfig
    {
        $config = new EndpointConfig();
        $config->setDeleteUrlparam($this->config[$nodeName]['deleteUrl']);
        $config->setPayloadAttribute($this->config[$nodeName]['payloadAttribute']);
        $config->setSendUrlParam($this->config[$nodeName]['sendUrl']);
        return $config;
    }
}
