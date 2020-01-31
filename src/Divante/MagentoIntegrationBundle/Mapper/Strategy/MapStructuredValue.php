<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Cassandra\Map;
use Divante\MagentoIntegrationBundle\Mapper\MapperContext;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Webservice\Data\DataObject\Element;

use Divante\MagentoIntegrationBundle\Helper\MapperHelper;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MapStructuredValue
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
class MapStructuredValue extends AbstractMapStrategy
{
    const ALLOWED_TYPES_ARRAY = MapperHelper::STRUCTURED_TYPES;

    /** @var MapperContext */
    protected $mapperContext;

    /**
     * MapLocalizedValue constructor.
     * @param MapperContext $mapperContext
     * @param TranslatorInterface    $translator
     */
    public function __construct(MapperContext $mapperContext, TranslatorInterface $translator)
    {
        $this->mapperContext = $mapperContext;
        parent::__construct($translator);
    }

    /**
     * @param Element     $field
     * @param \stdClass   $obj
     * @param array       $configuration
     * @param string|null $language
     * @param mixed       $definition
     * @param string      $className
     */
    public function map(Element $field, \stdClass &$obj, array $configuration, $language, $definition, $className): void
    {
        if (!$field->value) {
            return;
        }
        /** @var ClassDefinition\Data\Localizedfields $fields */
        $fields = $definition->getFieldDefinitions()[MapperHelper::LOCALIZED_FIELD_TYPE];
        $prefix = substr(
            $field->name,
            0,
            strrpos($field->name, sprintf("%s", MapperHelper::LOCALIZED_FIELD_TYPE))
        );
        /** @var Element $value */
        foreach ($field->value as $value) {
            if (!$value->language || $value->language == $language) {
                $fieldDefinition = $fields->getFielddefinition($value->name);
                $value->label = $fieldDefinition ? $fieldDefinition->title : $value->name;
                $value->name = $prefix . $value->name;
                $this->mapperContext->map($value, $obj, $configuration, $language, $definition, $className);
            }
        }
    }
}
