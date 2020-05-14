<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\ClassificationStore;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\ClassificationStore\MapBooleanKey;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

/**
 * Class MapCheckboxKey
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy\ClassificationStore
 */
class MapCheckboxKey extends MapBooleanKey
{
    const ALLOWED_TYPES_ARRAY = ['checkbox'];
    const TYPE = 'yesno';
}
