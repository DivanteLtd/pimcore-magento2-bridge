<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Helper;

/**
 * Class MapperEventTypes
 * @package Divante\MagentoIntegrationBundle\Domain\Helper
 */
class MapperEventTypes
{
    const PRE_PRODUCT_MAP = 'magento_integration.pre_product_map';
    const POST_PRODUCT_MAP = 'magento_integration.post_product_map';
    const PRE_CATEGORY_MAP = 'magento_integration.pre_category_map';
    const POST_CATEGORY_MAP = 'magento_integration.post_category_map';
}
