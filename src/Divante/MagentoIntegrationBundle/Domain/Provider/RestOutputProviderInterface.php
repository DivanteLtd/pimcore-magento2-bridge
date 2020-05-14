<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Provider;

use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\Model\EndpointConfig;

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
