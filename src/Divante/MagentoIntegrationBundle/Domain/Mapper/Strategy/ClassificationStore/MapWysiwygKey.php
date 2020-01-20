<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        12/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy\ClassificationStore;

use Divante\MagentoIntegrationBundle\Domain\Helper\MapperHelper;

/**
 * Class MapWysiwygKey
 * @package  Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy\ClassificationStore
 */
class MapWysiwygKey extends MapTextKey
{
    const TYPE = 'wysiwyg';
    const ALLOWED_TYPES_ARRAY = MapperHelper::WYSIWYG_TYPES;
}
