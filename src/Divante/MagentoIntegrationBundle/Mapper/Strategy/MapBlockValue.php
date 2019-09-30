<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        26/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;
use Divante\MagentoIntegrationBundle\Mapper\Strategy\MapTextValue;

/**
 * Class MapBlockValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
class MapBlockValue extends MapTextValue
{
    const TYPE = 'block';
    const ALLOWED_TYPES_ARRAY = MapperHelper::BLOCK_TYPES;
}
