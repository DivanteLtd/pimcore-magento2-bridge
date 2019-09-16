<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Symfony\Component\Translation\TranslatorInterface;
/**
 * Class MapBooleanKey
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
class MapBooleanKey extends AbstractMapKeyStrategy
{

    const ALLOWED_TYPES_ARRAY = ['booleanSelect'];
    const TYPE = 'yesno';

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
            'type'  => static::TYPE,
            'value' => $attribute['value'],
            'label' => $this->getLabel($field->getTitle(), $language)
        ];
        foreach ($names as $name) {
            $obj->{$attributeName} = $parsedData;
        }
    }
}
