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
 * Class MapUserValue
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
class MapUserValue extends MapSelectValue
{
    const TYPE = 'user';
    const ALLOWED_TYPES_ARRAY = MapperHelper::USER_TYPES;
}
