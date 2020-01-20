<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        15/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Helper;

/**
 * Class IntegrationHelper
 * @package Divante\MagentoIntegrationBundle\Domain\Helper
 */
class IntegrationHelper
{
    const RELATION_TYPE_PRODUCT = 0;
    const RELATION_TYPE_CATEGORY = 1;
    const RELATION_TYPE_ASSET = 2;
    const OBJECT_TYPE_PRODUCT = 'product';
    const OBJECT_TYPE_CATEGORY = 'category';
    const OBJECT_TYPE_ASSET = 'asset';

    const INTEGRATION_CONFIGURATION_MANDATORY_FIELDS_PRODUCT = [
        'name',
        'sku',
        'visibility',
        'is_active_in_pim',
        'url_key',
        'category_ids'
    ];
    const INTEGRATION_CONFIGURATION_MANDATORY_FIELDS_CATEGORY = [
        'name',
        'url_key'
    ];
    const PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE = 'configurable_attributes';
    const PRODUCT_TYPE_CONFIGURABLE = 'configurable';
}
