<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        01/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Provider;


/**
 * Class MagentoRestOutputProvider
 * @package Divante\MagentoIntegrationBundle\Provider
 */
class MagentoRestOutputProvider extends RestOutputProvider
{
    const CONFIG_PATH =
        '@DivanteMagentoIntegrationBundle/Resources/config/magentoProviderConfig.yaml';

    /**
     * @return string
     */
    public function getStoreViewsEndpointUrl(): string
    {
        return $this->config['getStoreViewsUrl'];
    }
}