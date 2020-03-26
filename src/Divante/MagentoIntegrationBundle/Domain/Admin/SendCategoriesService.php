<?php

namespace Divante\MagentoIntegrationBundle\Domain\Admin;

use Divante\MagentoIntegrationBundle\Domain\Admin\Request\GetIntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Domain\RemoteElementService;
use Divante\MagentoIntegrationBundle\Domain\Admin\AbstactSendService;
use Pimcore\Model\DataObject\IntegrationConfiguration;


/**
 * Class sendCategoriesService
 * @package Divante\MagentoIntegrationBundle\Domain\Admin
 */
class SendCategoriesService extends AbstactSendService
{
    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    protected function getObjectsRoot(IntegrationConfiguration $configuration): string
    {
        return $configuration->getCategoryRoot();
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    protected function getObjectClass(IntegrationConfiguration $configuration): string
    {
        return $configuration->getCategoryClass();
    }
}
