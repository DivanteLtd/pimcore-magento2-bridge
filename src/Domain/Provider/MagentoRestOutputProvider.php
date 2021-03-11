<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Provider;

use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\Model\EndpointConfig;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class MagentoRestOutputProvider
 * @package Divante\MagentoIntegrationBundle\Provider
 */
class MagentoRestOutputProvider implements RestOutputProviderInterface
{
    const CONFIG_PATH =
        '@DivanteMagentoIntegrationBundle/Resources/config/magentoProviderConfig.yaml';

    /** @var array */
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
     * @return string
     */
    public function getStoreViewsEndpointUrl(): string
    {
        return $this->config['getStoreViewsUrl'];
    }

    /**
     * @return EndpointConfig
     */
    public function getAssetConfig(): EndpointConfig
    {
        return $this->getConfig('asset');
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
}
