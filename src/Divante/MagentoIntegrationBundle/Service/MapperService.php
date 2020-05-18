<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Model\Mapping\FromColumn;
use Divante\MagentoIntegrationBundle\Helper\MapperHelper;
use Divante\MagentoIntegrationBundle\Mapper\MapperContext;
use Pimcore\Cache\Core\Exception\InvalidArgumentException;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class MapperService
 * @package Divante\MagentoIntegrationBundle\Service
 */
class MapperService
{
    const ELEMENTS_PROPERTY_NAME = 'elements';

    /** @var MapperContext */
    protected $mapper;

    /**
     * MapperService constructor.
     * @param MapperContext $mapper
     */
    public function __construct(MapperContext $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param mixed                    $out
     * @param IntegrationConfiguration $configuration
     * @param string                   $type
     * @return \stdClass
     */
    public function map($out, IntegrationConfiguration $configuration, string $type): \stdClass
    {
        $object = new \stdClass();
        switch ($type) {
            case (MapperHelper::OBJECT_TYPE_PRODUCT):
                $mappingArray = $configuration->getDecodedProductMapping();
                break;
            case (MapperHelper::OBJECT_TYPE_CATEGORY):
                $mappingArray = $configuration->getDecodedCategoryMapping();
                break;
            default:
                throw new InvalidArgumentException("Element type not known");
        }

        $objectClass = DataObject\ClassDefinition::getByName($out->className);
        foreach ($out as $key => $value) {
            if ($key != self::ELEMENTS_PROPERTY_NAME) {
                $object->{$key} = $value;
            } else {
                $elements = new \stdClass();
                $value = array_filter($value, function ($elem) use ($objectClass, $configuration) {
                    return $this->canAttributeBeProcessed($elem, $objectClass, $configuration);
                });
                /** @var Element $element */
                foreach ($value as $element) {
                    $label = $objectClass->getFieldDefinition($element->name)->getTitle();
                    $element->label = $label ? $label : $element->name;
                    $this->mapper->map(
                        $element,
                        $elements,
                        $mappingArray,
                        $configuration->getDefaultLanguage(),
                        $objectClass,
                        $out->className
                    );
                }
                $object->{self::ELEMENTS_PROPERTY_NAME} = $elements;
            }
        }
        $this->removeUnusedAttributes($object);
        return $object;
    }

    /**
     * @param \stdClass $object
     */
    protected function removeUnusedAttributes(\stdClass &$object)
    {
        unset($object->notes);
        unset($object->childs);
    }

    /**
     * @param Element                    $element
     * @param DataObject\ClassDefinition $definition
     * @param IntegrationConfiguration   $configuration
     * @return bool
     */
    protected function canAttributeBeProcessed(
        Element $element,
        DataObject\ClassDefinition $definition,
        IntegrationConfiguration $configuration
    ): bool {
        return !(
            $element->type == MapperHelper::CLASSIFICATION_STORE_TYPE
            &&
            $definition->getFieldDefinition($element->name)->storeId
            != $configuration->getDefaultClassificationStore()
        );
    }

    /**
     * @param DataObject\ClassDefinition $class
     *
     * @return array
     * @throws \Exception
     */
    public function getClassDefinitionForFieldSelection(DataObject\ClassDefinition $class): array
    {
        $fields = $class->fieldDefinitions;
        $result = array();
        foreach ($fields as $field) {
            if ($field instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                $localizedFields = $field->getFieldDefinitions();
                foreach ($localizedFields as $localizedField) {
                    $result[] = $this->getFieldConfiguration($localizedField);
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Fieldcollections) {
                foreach ($field->getAllowedTypes() as $type) {
                    $definition = DataObject\Fieldcollection\Definition::getByKey($type);

                    $fieldDefinition = $definition->getFieldDefinitions();

                    foreach ($fieldDefinition as $fieldcollectionField) {
                        $resultField = $this->getFieldConfiguration($fieldcollectionField);

                        $resultField->setIdentifier(
                            "fieldcollection_" . $field->getName() . "_" . $type . "_" . $resultField->getIdentifier()
                        );

                        $result[] = $resultField;
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Classificationstore) {
                continue;
            } else {
                $result[] = $this->getFieldConfiguration($field);
            }
        }
        return $result;
    }


    /**
     * @param DataObject\ClassDefinition\Data $field
     * @return FromColumn
     */
    protected function getFieldConfiguration(DataObject\ClassDefinition\Data $field): FromColumn
    {
        $fromColumn = new FromColumn();
        $fromColumn->setLabel($field->getTitle() ? $field->getTitle() : $field->getName());
        $fromColumn->setIdentifier($field->getName());

        return $fromColumn;
    }

    /**
     * @param \stdClass $object
     * @return array
     */
    public function getAttributesChecksum(\stdClass $object): array
    {
        $attributes = [];
        foreach ($object->elements as $key => $element) {
            $attributes[$key] = $element['type'];
        }
        ksort($attributes);
        return ['algo' => 'md5', 'value' => md5(json_encode($attributes))];
    }

    /**
     * @param mixed    $out
     * @param Concrete $object
     * @throws \Exception
     */
    public function loadSelectFieldData(&$out, Concrete $object): void
    {
        $this->loadKeys($out->elements, $object);
    }

    /**
     * @param $field
     *
     * @return array
     */
    private function getAllFieldDataChilds($field)
    {
        $dataChilds = [];
        foreach ($field->childs as $field) {
            if ($field instanceof DataObject\ClassDefinition\Layout && $field->getChildren()) {
                $dataChilds = array_merge($dataChilds, $this->getAllFieldDataChilds($field));
            } else {
                $dataChilds[] = $field;
            }
        }
        return $dataChilds;
    }

    /**
     * @param string   $elementName
     * @param Concrete $object
     * @return null
     */
    protected function getOptionsForSelect(string $elementName, Concrete $object)
    {
        $localizedFields = $object->getClass()->getFieldDefinition('localizedfields');
        if ($localizedFields) {
            $localizedFieldsArray   = $localizedFields->getReferencedFields();
            $localizedFieldsArray[] = $localizedFields;
            foreach ($localizedFieldsArray as $localizedFields) {
                $dataFields = $this->getAllFieldDataChilds($localizedFields);
                foreach ($dataFields as $field) {
                    if ($field->name == $elementName) {
                        return $field->getOptions();
                    }
                }
            }
        }
        $fielDefinition = $object->getClass()->getFieldDefinition($elementName);
        if ($fielDefinition instanceof DataObject\ClassDefinition\Data) {
            return $fielDefinition->getOptions();
        }
        return null;
    }

    /**
     * @param array    $mappedObjectElements
     * @param Concrete $object
     * @throws \Exception
     */
    protected function loadKeys(array &$mappedObjectElements, Concrete $object): void
    {
        foreach ($mappedObjectElements as $element) {
            if ($element->type == 'select') {
                $options = $this->getOptionsForSelect($element->name, $object);
                if (!$options) {
                    continue;
                }
                foreach ($options as $keyvalue) {
                    if ($keyvalue['value'] == $element->value) {
                        $element->value = $keyvalue;
                        break;
                    }
                }
            } elseif ($element->type === 'multiselect') {
                $options = $this->getOptionsForSelect($element->name, $object);
                if (!$options || $element->value === null) {
                    continue;
                }
                foreach ($element->value as $id => $value) {
                    foreach ($options as $keyvalue) {
                        if ($value == $keyvalue['value']) {
                            $element->value[$id] = $keyvalue;
                            break;
                        }
                    }
                }
            } elseif (in_array($element->type, MapperHelper::STRUCTURED_TYPES)) {
                $this->loadKeys($element->value, $object);
            } elseif (in_array($element->type, MapperHelper::OBJECT_BRICKS_TYPES)) {
                foreach ($element->value as $brick) {
                    $this->loadBrickKeys($brick);
                }
            }
        }
    }

    /**
     * @param Element $brick
     * @throws \Exception
     */
    protected function loadBrickKeys(Element &$brick)
    {
        if (!$brick->value) {
            return;
        }
        /** @var DataObject\Objectbrick\Definition $brickDefinition */
        $brickDefinition = DataObject\Objectbrick\Definition::getByKey($brick->type);
        foreach ($brick->value as $singleField) {
            $definition = $brickDefinition->getFieldDefinition($singleField->name);
            if ($definition instanceof DataObject\ClassDefinition\Data\Multiselect) {
                $options = $definition->getOptions();
                $values = [];
                foreach ($singleField->value as $value) {
                    $values = array_filter($options, function ($elem) use ($value) {
                        return $elem['value'] == $value;
                    });
                }
                $singleField->value = $values;
            } elseif ($definition instanceof DataObject\ClassDefinition\Data\Select) {
                $options = $definition->getOptions();
                foreach ($options as $keyvalue) {
                    if ($keyvalue['value'] == $singleField->value) {
                        $singleField->value = $keyvalue;
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        return ['name', 'sku', 'is_active_in_pim', 'url_key', 'visibility', 'category_ids'];
    }

    /**
     * @param \stdClass $object
     * @param Concrete  $product
     */
    public function enrichConfigurableProduct(\stdClass &$object, Concrete $product): void
    {
        $configurableAttributes = $product->getProperty('configurable_attributes');
        if (!$configurableAttributes) {
            return;
        }
        $configurableAttributesArray = explode(',', $configurableAttributes);
        $modifiedAttributesArray = [];
        foreach ($configurableAttributesArray as $attribute) {
            $attribute = str_replace('-', '_', strtolower($attribute));
            if ($object->elements->{$attribute}) {
                $element = $object->elements->{$attribute};
                unset($object->elements->{$attribute});
                $object->elements->{$attribute  . '_conf'} = $element;
                $modifiedAttributesArray[] = $attribute  . '_conf';
            }
        }
        $modifiedAttributes = implode(',', $modifiedAttributesArray);
        if (!$object->properties) {
            return;
        }
        foreach ($object->properties as $property) {
            if ($property->name == IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE) {
                $property->data = $modifiedAttributes;
            }
        }
    }

}
