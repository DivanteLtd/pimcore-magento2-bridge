<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        23/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\AbstractMapStrategy;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration\AttributeType;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperHelper;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class MapMultiObjectValue
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
class MapMultiObjectValue extends AbstractMapStrategy
{
    const TYPE = 'multiobject';
    const ALLOWED_TYPES_ARRAY = MapperHelper::MULTI_OBJECT_TYPES;

    /**
     * @param Element $field
     * @param \stdClass $obj
     * @param array $arrayMapping
     * @param string|null $language
     * @param mixed $definition
     * @param $integratedObject
     * @return void
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $integratedObject): void
    {
        if (!$field->value) {
            return;
        }
        $names      = $this->getFieldNames($field, $arrayMapping);
        $parsedData = [
            'type'  => self::TYPE,
            'value' => $this->getFieldValues($field),
            'label' => $this->getLabel($field, $language),
            static::ATTR_CONF => $this->getAttrConf($field, $arrayMapping)
        ];

        foreach ($names as $name) {
            $thumbnail = $this->getThumbnail($field, $arrayMapping, $name);
            if ($thumbnail) {
                foreach ($parsedData["value"] as $key => $element) {
                    if ($parsedData["value"][$key]['id']) {
                        $parsedData["value"][$key]['id'] .= AttributeType::THUMBNAIL_CONCAT . $thumbnail;
                    }
                }
            }
            $obj->{$name} = $parsedData;
            foreach ($parsedData["value"] as $key => $element) {
                if (strpos($parsedData["value"][$key]['id'], AttributeType::THUMBNAIL_CONCAT) !== false) {
                    $parsedData["value"][$key]['id'] = substr(
                        $parsedData["value"][$key]['id'],
                        0,
                        strpos($parsedData["value"][$key]['id'], AttributeType::THUMBNAIL_CONCAT)
                    );
                }
            }
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
