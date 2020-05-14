<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        26/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperHelper;
use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\MapTextValue;

/**
 * Class MapBlockValue
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
class MapBlockValue extends MapTextValue
{
    const TYPE = 'block';
    const ALLOWED_TYPES_ARRAY = MapperHelper::BLOCK_TYPES;
}
