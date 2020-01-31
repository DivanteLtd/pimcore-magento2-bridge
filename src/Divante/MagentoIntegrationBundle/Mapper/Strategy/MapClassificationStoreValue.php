<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        06/04/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;
use Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore\MapKeyStrategyInterface;
use Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data\Classificationstore;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class MapClassificationStoreValue
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy
 */
class MapClassificationStoreValue extends AbstractMapStrategy
{
    const ALLOWED_TYPES_ARRAY = MapperHelper::CLASSIFICATION_STORE_TYPES;

    /** @var MapKeyStrategyInterface[] */
    private $strategies = [];

    /**
     * @param MapKeyStrategyInterface $strategy
     */
    public function addStrategy(MapKeyStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @param Element     $field
     * @param \stdClass   $obj
     * @param array       $arrayMapping
     * @param string|null $language
     * @param             $definition
     * @param string      $className
     * @return void
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $className): void
    {
        if (!is_array($field->value['groups'])) {
            return;
        }
        $fieldDefinition = $definition->getFieldDefinition($field->name);
        if ($fieldDefinition instanceof Classificationstore) {
            return;
        }
        $storeId = (int) $fieldDefinition->storeId;
        foreach ($field->value['groups'] as $group) {
            $groupConfig =  GroupConfig::getById($group['id']);
            $group['name']  = $field->name . '_' . $group['name'];
            if (!$group['keys'] || !$groupConfig instanceof GroupConfig) {
                continue;
            }
            $relations = $groupConfig->getRelations();
            foreach ($relations as $relation) {
                $attributeDefinition = KeyConfig::getByName($relation->name, $storeId);
                $value = null;

                foreach ($group['keys']['default'] as $attribute) {
                    if ($attribute['name'] == $attributeDefinition->getName()) {
                        $value = $attribute['value'];
                        break;
                    }
                }
                $attribute = [
                    'name' => $attributeDefinition->getName(),
                    'description' => $attributeDefinition->getDefinition(),
                    'value' => $value
                ];
                foreach ($this->strategies as $strategy) {
                    if ($strategy->canProcess($attributeDefinition)) {
                        $mappedAttrNames = $strategy->mapStringNames($attributeDefinition->getName(), $group['name'], $arrayMapping);
                        foreach ($mappedAttrNames as $mappedAttrName) {
                            if (property_exists($obj, $mappedAttrName) && $obj->{$mappedAttrName}['value'] !== null) {
                                continue;
                            } else {
                                $strategy->map($attributeDefinition, $attribute, $group, $obj, $arrayMapping,
                                    $language);
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
}
