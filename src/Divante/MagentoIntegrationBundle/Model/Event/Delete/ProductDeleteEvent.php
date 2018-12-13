<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        19/09/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Model\Event\Delete;

use Divante\MagentoIntegrationBundle\Model\Event\IntegratedObjectEvent;

/**
 * Class ProductDeleteEvent
 * @package Divante\MagentoIntegrationBundle\Model\Event\Delete
 */
class ProductDeleteEvent extends IntegratedObjectEvent
{
    const NAME = 'magento_integration.product.delete';
}
