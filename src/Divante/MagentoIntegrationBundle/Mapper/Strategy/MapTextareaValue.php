<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;

/**
 * Class MapTextareaValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
class MapTextareaValue extends MapTextValue
{
    const TYPE = 'textarea';
    const ALLOWED_TYPES_ARRAY = MapperHelper::TEXT_AREA_TYPES;
}
