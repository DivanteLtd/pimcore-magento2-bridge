<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\ObjectMapperBundle\Mapper\Strategy;

use Divante\ObjectMapperBundle\Helper\MapperHelper;
use Divante\ObjectMapperBundle\Mapper\Strategy\MapTextValue;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class MapMultiSelectValue
 * @package Divante\ObjectMapperBundle\Service\MapperService\Strategy
 */
class MapMultiSelectValue extends MapTextValue
{
    const TYPE = 'multiselect';
    const ALLOWED_TYPES_ARRAY = MapperHelper::MULTI_SELECT_TYPES;

    /**
     * @param Element   $field
     * @param \stdClass $obj
     * @param array     $arrayMapping
     * @param null      $language
     * @param string    $className
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $className): void
    {
        if (!$field->value) {
            return;
        }
        $names = $this->getFieldNames($field, $arrayMapping);
        $parsedData = [
            'value' => $this->getFieldValues($field, $language),
            'type' => static::TYPE,
            'label' => $this->getLabel($field, $language)
        ];

        foreach ($names as $name) {
            $obj->{$name} = $parsedData;
        }
    }

    /**
     * @param Element $field
     * @param         $language
     */
    protected function getFieldValues(Element $field, $language): void
    {
        $values = [];
        foreach ($field->value as $value) {
            $values[] =
                (object)[
                    'key'   => $this->translator->trans($value['key'], [], null, $language),
                    'value' => $value['value']
                ];
        }
    }
}
