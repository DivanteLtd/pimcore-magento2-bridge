<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\ObjectMapperBundle\Mapper\Strategy;

use Divante\ObjectMapperBundle\Mapper\MapperContextInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Webservice\Data\DataObject\Element;

use Divante\ObjectMapperBundle\Helper\MapperHelper;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MapStructuredValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
class MapStructuredValue extends AbstractMapStrategy
{
    const ALLOWED_TYPES_ARRAY = MapperHelper::STRUCTURED_TYPES;

    /** @var MapperContextInterface */
    protected $mapperContext;

    /**
     * MapLocalizedValue constructor.
     * @param MapperContextInterface $mapperContext
     * @param TranslatorInterface    $translator
     */
    public function __construct(MapperContextInterface $mapperContext, TranslatorInterface $translator)
    {
        $this->mapperContext = $mapperContext;
        parent::__construct($translator);
    }

    /**
     * @param Element     $field
     * @param \stdClass   $obj
     * @param array       $configuration
     * @param string|null $language
     * @param string      $className
     */
    public function map(Element $field, \stdClass &$obj, array $configuration, $language, $className): void
    {
        $classDefinition = ClassDefinition::getByName($className);
        if (!$field->value || !$classDefinition instanceof ClassDefinition) {
            return;
        }
        /** @var ClassDefinition\Data\Localizedfields $fields */
        $fields = array_key_exists($field->name, $classDefinition->getFieldDefinitions())
            ? $classDefinition->getFieldDefinitions()[$field->name]
            : null;
        /** @var Element $value */
        foreach ($field->value as $value) {
            if (!$value->language || $value->language == $language) {
                $fieldDefinition = $fields->getFielddefinition($value->name);
                $value->label = $fieldDefinition ? $fieldDefinition->title : $value->name;
                $this->mapperContext->map($value, $obj, $configuration, $language, $className);
            }
        }
    }
}
