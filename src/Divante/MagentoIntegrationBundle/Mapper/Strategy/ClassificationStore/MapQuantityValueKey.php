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
 * Class MapQuantityValueKey
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
class MapQuantityValueKey extends AbstractMapKeyStrategy
{

    const ALLOWED_TYPES_ARRAY = ['inputQuantityValue', 'quantityValue'];
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
        $value = $attribute['value']['value'];
        if ($attribute['value']['unitAbbreviation'] != "") {
            $value .= ' ' . $attribute['value']['unitAbbreviation'];
        }

        $parsedData = [
            'type' => static::TYPE,
            'label' => $this->getLabel($field->getTitle(), $language),
            'value' => $value
        ];
        foreach ($names as $name) {
            $obj->{$name} = $parsedData;
        }
    }
}
