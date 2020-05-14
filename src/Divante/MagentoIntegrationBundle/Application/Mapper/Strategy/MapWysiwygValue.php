<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\MapTextValue;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperHelper;

/**
 * Class MapWysiwygValue
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
class MapWysiwygValue extends MapTextValue
{
    const TYPE = 'wysiwyg';
    const ALLOWED_TYPES_ARRAY = MapperHelper::WYSIWYG_TYPES;
}
