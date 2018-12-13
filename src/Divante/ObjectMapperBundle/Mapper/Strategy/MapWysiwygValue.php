<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        07/09/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\ObjectMapperBundle\Mapper\Strategy;

use Divante\ObjectMapperBundle\Helper\MapperHelper;

/**
 * Class MapWysiwygValue
 * @package Divante\ObjectMapperBundle\Mapper\Strategy
 */
class MapWysiwygValue extends MapTextValue
{
    const TYPE = 'wysiwyg';
    const ALLOWED_TYPES_ARRAY = MapperHelper::WYSIWYG_TYPES;
}
