<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        26/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;

use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class AttributeType
 * @package Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration
 */
abstract class AttributeType
{
    const FILTERABLE = 'filterable';
    const SEARCHABLE = 'searchable';
    const COMPARABLE = 'comparable';
    const VISIBLE_ON_FRONT = 'visible_on_front';
    const PRODUCT_LISTING = 'used_in_product_listing';

    const IMAGE_DEFAULT = "original";
}
