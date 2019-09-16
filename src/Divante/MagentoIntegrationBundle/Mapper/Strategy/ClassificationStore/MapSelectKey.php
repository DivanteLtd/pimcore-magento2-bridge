<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

use Divante\MagentoIntegrationBundle\Mapper\Strategy\MapSelectValue;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

/**
 * Class MapSelectKey
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
class MapSelectKey extends AbstractMapKeyStrategy
{
    const ALLOWED_TYPES_ARRAY = ['select', 'multiselect'];
    const SINGLE_TYPE = 'select';
    const MULTI_TYPE = 'multiselect';

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
        $names         = $this->mapStringNames($attribute['name'], $group['name'], $arrayMapping);
        $valuesWithKey = $this->getFieldValue($field, $attribute, $language);

        $parsedData = [
            'type'  => MapSelectValue::TYPE,
            'label' => $this->getLabel($field->getTitle(), $language),
            'value' => $valuesWithKey
        ];

        if (count($valuesWithKey) == 1) {
            $parsedData['value'] = $parsedData['value'][0];
            $parsedData['type']  = self::SINGLE_TYPE;
        } else {
            $parsedData['type']  = self::MULTI_TYPE;
        }
        foreach ($names as $name) {
            $obj->{$name} = $parsedData;
        }
    }

    /**
     * @param KeyConfig $field
     * @return array
     */
    protected function getOptions(KeyConfig $field): array
    {
        $selectDefinition = json_decode($field->getDefinition());
        return array_map(
            function ($elem) {
                return ['key' => $elem->key, 'value' => $elem->value];
            },
            $selectDefinition->options
        );
    }

    /**
     * @param KeyConfig $field
     * @param array     $attribute
     * @param           $language
     * @return array|null
     */
    protected function getFieldValue(KeyConfig $field, array $attribute, $language)
    {
        $options       = $this->getOptions($field);
        $valuesWithKey = null;
        $valuesArray   = is_array($attribute['value']) ? $attribute['value'] : [$attribute['value']];
        foreach ($valuesArray as $singleValue) {
            foreach ($options as $struct) {
                if ($struct['value'] == $singleValue) {
                    $struct['key']   = $this->translator->trans($struct['key'], [], null, $language);
                    $valuesWithKey[] = $struct;
                    break;
                }
            }
        }
        return $valuesWithKey;
    }
}
