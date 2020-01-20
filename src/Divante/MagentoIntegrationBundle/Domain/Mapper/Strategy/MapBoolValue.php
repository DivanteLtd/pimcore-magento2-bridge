<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Domain\Helper\MapperHelper;

/**
 * Class MapBoolValue
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
class MapBoolValue extends MapTextValue
{
    const TYPE = 'yesno';
    const ALLOWED_TYPES_ARRAY = MapperHelper::BOOL_TYPES;
}
