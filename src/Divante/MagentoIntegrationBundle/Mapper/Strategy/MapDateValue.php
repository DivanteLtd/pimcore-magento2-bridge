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
 * Class MapDateValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
class MapDateValue extends MapTextValue
{
    const TYPE = 'datetime';
    const ALLOWED_TYPES_ARRAY = MapperHelper::DATE_TYPES;
}
