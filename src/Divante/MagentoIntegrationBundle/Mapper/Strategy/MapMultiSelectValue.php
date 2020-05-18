<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;
use Divante\MagentoIntegrationBundle\Mapper\Strategy\MapTextValue;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class MapMultiSelectValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
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
     * @param mixed     $definition
     * @param string    $className
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $className): void
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
     * @return array
     */
    protected function getFieldValues(Element $field, $language)
    {
        $values = [];
        foreach ($field->value as $value) {
            $key = is_array($value) ? $value['key'] : $value;
            $values[] =
                (object)[
                    'key'   => $this->translator->trans($key, [], null, $language),
                    'value' => is_array($value) ? $value['value'] : $value
                ];
        }
        return $values;
    }
}
