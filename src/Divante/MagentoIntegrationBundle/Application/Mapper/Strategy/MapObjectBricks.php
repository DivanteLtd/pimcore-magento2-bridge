<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        26/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\AbstractMapStrategy;
use Divante\MagentoIntegrationBundle\Application\Mapper\MapperContext;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\Webservice\Data\DataObject\Element;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperHelper;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MapObjectBricks
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
class MapObjectBricks extends AbstractMapStrategy
{
    const TYPE = MapperHelper::OBJECT_BRICKS_TYPE;
    const ALLOWED_TYPES_ARRAY = MapperHelper::OBJECT_BRICKS_TYPES;
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
     * @param array       $arrayMapping
     * @param string|null $language
     * @param string      $className
     * @param mixed       $definition
     * @throws \Exception
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $className): void
    {
        /** @var Element $brick */
        foreach ($field->value as $brick) {
            /** @var Objectbrick\Definition $brickDefinition */
            $brickDefinition = Objectbrick\Definition::getByKey($brick->type);

            if (!$brickDefinition instanceof Objectbrick\Definition
                || ($brick->language && $brick->language != $language )
            ) {
                continue;
            }
            foreach ($brick->value as $attribute) {
                $fieldDefinition = $brickDefinition->getFieldDefinition($attribute->name);
                $attribute->label = $fieldDefinition ? $fieldDefinition->getTitle() : $attribute->name;
                $attribute->name = $field->name . '_' . $brick->type . '_' . $attribute->name;
                $this->mapperContext->map($attribute, $obj, $arrayMapping, $language, $brickDefinition, $className);
            }
        }
    }
}
