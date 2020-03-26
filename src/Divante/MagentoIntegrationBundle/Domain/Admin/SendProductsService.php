<?php

namespace Divante\MagentoIntegrationBundle\Domain\Admin;

use Divante\MagentoIntegrationBundle\Domain\Admin\Request\GetIntegrationConfiguration;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Domain\Admin\AbstactSendService;

/**
 * Class SendProductsService
 * @package Divante\MagentoIntegrationBundle\Domain\Admin
 */
class SendProductsService extends AbstactSendService
{
    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    protected function getObjectsRoot(IntegrationConfiguration $configuration): string
    {
        return $configuration->getProductRoot();
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    protected function getObjectClass(IntegrationConfiguration $configuration): string
    {
        return $configuration->getProductClass();
    }
}
