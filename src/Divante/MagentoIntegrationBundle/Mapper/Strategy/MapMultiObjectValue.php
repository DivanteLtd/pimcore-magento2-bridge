<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        23/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class MapMultiObjectValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
class MapMultiObjectValue extends AbstractMapStrategy
{
    const TYPE = 'multiobject';
    const ALLOWED_TYPES_ARRAY = MapperHelper::MULTI_OBJECT_TYPES;

    /**
     * @param Element     $field
     * @param \stdClass   $obj
     * @param array       $arrayMapping
     * @param string|null $language
     * @param mixed       $definition
     * @param string      $className
     * @return void
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $className): void
    {
        if (!$field->value) {
            return;
        }
        $names      = $this->getFieldNames($field, $arrayMapping);
        $parsedData = [
            'type'  => self::TYPE,
            'value' => $this->getFieldValues($field),
            'label' => $this->getLabel($field, $language)
        ];

        foreach ($names as $name) {
            $obj->{$name} = $parsedData;
        }
    }

    /**
     * @param Element $field
     * @return array
     */
    protected function getFieldValues(Element $field): array
    {
        $values = [];
        foreach ($field->value as $element) {
            if (in_array($field->type, MapperHelper::IMAGE_TYPES)) {
                $values[] = array('id' => $element['image__image'], 'type' => 'asset');
            } else {
                $values[] = $element;
            }
        }
        return $values;
    }
}
