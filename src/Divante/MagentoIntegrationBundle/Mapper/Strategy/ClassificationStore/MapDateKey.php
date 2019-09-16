<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

/**
 * Class MapDateKey
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
class MapDateKey extends MapTextKey
{

    const ALLOWED_TYPES_ARRAY = [
        'date',
        'datetime'
    ];
    const TYPE = 'datetime';
}
