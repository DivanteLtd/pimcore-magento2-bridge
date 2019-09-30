<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;

/**
 * Class MapTextValue
 * @package  Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
class MapTextAreaKey extends MapTextKey
{
    const TYPE = 'textarea';
    const ALLOWED_TYPES_ARRAY = MapperHelper::TEXT_AREA_TYPES;
}
