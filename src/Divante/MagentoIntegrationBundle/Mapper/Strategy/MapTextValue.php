<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Pimcore\Model\Webservice\Data\DataObject\Element;
use Divante\MagentoIntegrationBundle\Helper\MapperHelper;

/**
 * Class MapTextValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
class MapTextValue extends AbstractMapStrategy
{
    const TYPE = 'text';
    const ALLOWED_TYPES_ARRAY = MapperHelper::TEXT_TYPES;

    /**
     * @param Element     $field
     * @param \stdClass   $obj
     * @param array       $arrayMapping
     * @param string|null $language
     * @param mixed       $definition
     * @param string      $className
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $className): void
    {
        $names      = $this->getFieldNames($field, $arrayMapping);
        $parsedData = [
            'value' => $field->value !== "" ? $field->value : null,
            'type'  => static::TYPE,
            'label' => $this->getLabel($field, $language)
        ];

        foreach ($names as $name) {
            $obj->{$name} = $parsedData;
        }
    }
}
