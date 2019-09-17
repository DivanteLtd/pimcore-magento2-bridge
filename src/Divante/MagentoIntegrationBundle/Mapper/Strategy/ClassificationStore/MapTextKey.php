<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

/**
 * Class MapTextKey
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
class MapTextKey extends AbstractMapKeyStrategy
{

    const ALLOWED_TYPES_ARRAY = [
        'rgbaColor',
        'input',
        'numeric',
        'slider',
        'table',
        'time'
    ];
    const TYPE = 'text';

    /**
     * @param KeyConfig   $field
     * @param array       $attribute
     * @param array       $group
     * @param \stdClass   $obj
     * @param array       $arrayMapping
     * @param string|null $language
     * @return void
     */
    public function map(
        KeyConfig $field,
        array $attribute,
        array $group,
        \stdClass &$obj,
        array $arrayMapping,
        $language
    ): void {
        $names = $this->mapStringNames($attribute['name'], $group['name'], $arrayMapping);
        $parsedData = [
            'type' => static::TYPE,
            'value' => $attribute['value'],
            'label' => $this->getLabel($field->getTitle(), $language)
        ];
        foreach ($names as $name) {
            $obj->{$name} = $parsedData;
        }
    }
}
