<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\AbstractMapStrategy;
use Pimcore\Model\Webservice\Data\DataObject\Element;

use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperHelper;

/**
 * Class MapQuantityValue
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
class MapQuantityValue extends AbstractMapStrategy
{
    const TYPE = 'quantityValue';
    const ALLOWED_TYPES_ARRAY = MapperHelper::QUANTITY_VALUE_TYPES;

    /**
     * @param Element $field
     * @param \stdClass $obj
     * @param array $arrayMapping
     * @param string|null $language
     * @param mixed $definition
     * @param $integratedObject
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $integratedObject): void
    {
        $names      = $this->getFieldNames($field, $arrayMapping);
        $parsedData = [
            'value' => $field->value['value'],
            'unit'  => $field->value['unitAbbreviation'],
            'type'  => static::TYPE,
            'label' => $this->getLabel($field, $language),
            static::ATTR_CONF => $this->getAttrConf($field, $arrayMapping)
        ];
        foreach ($names as $name) {
            $obj->{$name} = $parsedData;
        }
    }
}
