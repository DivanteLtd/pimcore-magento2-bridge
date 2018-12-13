<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        20/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service\Category;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Service\AbstractObjectUpdateStatusService;

/**
 * Class CategoryStatusService
 * @package Divante\MagentoIntegrationBundle\Service\Category
 */
class CategoryStatusService extends AbstractObjectUpdateStatusService
{
    const OBJECT_TYPE = IntegrationHelper::IS_CATEGORY;

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    public function getObjectClass(IntegrationConfiguration $configuration)
    {
        return $configuration->getCategoryClass();
    }
}
