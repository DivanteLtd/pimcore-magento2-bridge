<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        01/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Provider;

use Divante\MagentoIntegrationBundle\Model\Configuration\EndpointConfig;

/**
 * Interface RestOutputProviderInterface
 * @package Divante\MagentoIntegrationBundle\Provider
 */
interface RestOutputProviderInterface
{
    public function parseConfig(): void;
    public function getAssetConfig(): EndpointConfig;
    public function getProductConfig(): EndpointConfig;
    public function getCategoryConfig(): EndpointConfig;
    public function getStoreViewsEndpointUrl(): string;
}