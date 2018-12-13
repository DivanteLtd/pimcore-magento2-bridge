<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        12/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Event;

/**
 * Class Type
 * @package Divante\MagentoIntegrationBundle\Event
 */
class Type
{
    const PRE_PRODUCT_MAP   = 'magento_integration.pre_product_map';
    const POST_PRODUCT_MAP  = 'magento_integration.post_product_map';
    const PRE_CATEGORY_MAP  = 'magento_integration.pre_category_map';
    const POST_CATEGORY_MAP = 'magento_integration.post_category_map';
}