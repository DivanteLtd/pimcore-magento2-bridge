<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        15/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Helper;

/**
 * Class IntegrationHelper
 * @package Divante\MagentoIntegrationBundle\Helper
 */
class IntegrationHelper
{
    const IS_PRODUCT = 1;
    const IS_CATEGORY = 2;
    const IS_ASSET = 3;
    const OBJECT_TYPE_PRODUCT = 'product';
    const OBJECT_TYPE_CATEGORY = 'category';
    const SYNC_PROPERTY_NAME = 'synchronize-status';
    const SYNC_STATUS_SENT = 'SENT';
    const SYNC_STATUS_OK = 'SUCCESS';
    const SYNC_STATUS_ERROR = 'ERROR';
    const SYNC_STATUS_DELETE = 'DELETED';
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
    const DEFAULT_MAGENTO_STORE = 0;
    const INTEGRATED_OBJECT_DELETE_EVENT_NAME = 'magento_integration.object.delete';
    const INTEGRATED_OBJECT_PRE_DELETE_EVENT_NAME = 'magento_integration.object.pre-delete';
    const INTEGRATED_OBJECT_PRE_UPADTE_EVENT_NAME = 'magento_integration.object.pre-update';
    const INTEGRATED_OBJECT_UPADTE_EVENT_NAME = 'magento_integration.object.update';
    const PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE = 'configurable_attributes';
    const PRODUCT_TYPE_CONFIGURABLE = 'configurable';
}
