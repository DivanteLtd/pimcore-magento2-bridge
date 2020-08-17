<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperHelper;
use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\MapTextValue;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class MapMultiSelectValue
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
class MapMultiSelectValue extends MapTextValue
{
    const TYPE = 'multiselect';
    const ALLOWED_TYPES_ARRAY = MapperHelper::MULTI_SELECT_TYPES;

    /**
     * @param Element $field
     * @param \stdClass $obj
     * @param array $arrayMapping
     * @param null $language
     * @param mixed $definition
     * @param $outObject
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $outObject): void
    {
        if (!$field->value) {
            return;
        }
        $names = $this->getFieldNames($field, $arrayMapping);
        $parsedData = [
            'value' => $this->getFieldValues($field, $language),
            'type' => static::TYPE,
            'label' => $this->getLabel($field, $language),
            static::ATTR_CONF => $this->getAttrConf($field, $arrayMapping)
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
