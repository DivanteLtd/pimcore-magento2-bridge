<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\ObjectMapperBundle\Mapper\Strategy\ClassificationStore;

use Divante\ObjectMapperBundle\Helper\MapperHelper;

/**
 * Class MapTextValue
 * @package  Divante\ObjectMapperBundle\Mapper\Strategy\ClassificationStore
 */
class MapTextAreaKey extends MapTextKey
{
    const TYPE = 'textarea';
    const ALLOWED_TYPES_ARRAY = MapperHelper::TEXT_AREA_TYPES;
}
