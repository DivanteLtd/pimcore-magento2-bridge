<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        07/09/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;

/**
 * Class MapWysiwygValue
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy
 */
class MapWysiwygValue extends MapTextValue
{
    const TYPE = 'wysiwyg';
    const ALLOWED_TYPES_ARRAY = MapperHelper::WYSIWYG_TYPES;
}
