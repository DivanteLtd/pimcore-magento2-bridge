<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        12/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\ObjectMapperBundle\Mapper\Strategy\ClassificationStore;

use Divante\ObjectMapperBundle\Helper\MapperHelper;

/**
 * Class MapWysiwygKey
 * @package  Divante\ObjectMapperBundle\Mapper\Strategy\ClassificationStore
 */
class MapWysiwygKey extends MapTextKey
{
    const TYPE = 'wysiwyg';
    const ALLOWED_TYPES_ARRAY = MapperHelper::WYSIWYG_TYPES;
}
