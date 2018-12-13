<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        20/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service\Product;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Service\AbstractObjectUpdateStatusService;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;

/**
 * Class ProductStatusService
 * @package Divante\MagentoIntegrationBundle\Service\Product
 */
class ProductStatusService extends AbstractObjectUpdateStatusService
{
    const OBJECT_TYPE = IntegrationHelper::IS_PRODUCT;

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    public function getObjectClass(IntegrationConfiguration $configuration): string
    {
        return $configuration->getProductClass();
    }
}
