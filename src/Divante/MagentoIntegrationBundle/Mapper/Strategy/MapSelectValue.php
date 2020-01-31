<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class MapSelectValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
class MapSelectValue extends MapTextValue
{
    const TYPE = 'select';
    const ALLOWED_TYPES_ARRAY = MapperHelper::SELECT_TYPES;

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
            'type'  => static::TYPE,
            'label' => $this->getLabel($field, $language),
            'value' => $this->getFieldValue($field, $language)
        ];
        foreach ($names as $name) {
            $obj->{$name} = $parsedData;
        }
    }

    /**
     * @param Element $field
     * @param         $language
     * @return array|null|object[]
     */
    public function getFieldValue(Element $field, $language)
    {
        if (!is_array($field->value)) {
            return null;
        }
        $field->value['key'] = $this->translator->trans($field->value['key'], [], null, $language);
        return $field->value !== "" ? $field->value : null;

    }
}
