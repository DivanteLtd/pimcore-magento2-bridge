<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\MapStrategyInterface;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration\AttributeType;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperHelper;
use Pimcore\Model\Asset\Image\Thumbnail;
use Pimcore\Model\Webservice\Data\DataObject\Element;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractMapStrategy
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy
 */
abstract class AbstractMapStrategy implements MapStrategyInterface
{
    const ALLOWED_TYPES_ARRAY = [];

    const ATTR_CONF = 'attr_conf';

    /** @var TranslatorInterface  */
    protected $translator;

    /**
     * AbstractMapKeyStrategy constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Element $field
     * @param array   $mappingArray
     * @return array
     */
    protected function getFieldNames(Element $field, array $mappingArray)
    {
        if (array_key_exists($field->name, $mappingArray)) {
            $fieldName = $mappingArray[$field->name];
            $fieldName = array_filter($fieldName, function ($element) {
                return $element["strategy"] === null;
            });
            $fieldName = array_column($fieldName, "field");
        } else {
            $fieldName = [$field->name];
        }
        return str_replace('-', '_', array_map('strtolower', $fieldName));
    }

    /**
     * @param Element $field
     * @param array $mappingArray
     * @return array|mixed
     */
    protected function getAttrConf(Element $field, array $mappingArray)
    {
        $attrConf = [];
        if (
            array_key_exists($field->name, $mappingArray)
            && array_key_exists(0, $mappingArray[$field->name])
            && array_key_exists(static::ATTR_CONF, $mappingArray[$field->name][0])
        ) {
            $attrConf = $mappingArray[$field->name][0][static::ATTR_CONF];
        }
        return $attrConf;
    }

    /**
     * @param string $fieldName
     * @param array  $mappingArray
     * @return string
     */
    protected function mapStringName(string $fieldName, array $mappingArray): string
    {
        if (array_key_exists($fieldName, $mappingArray)) {
            $fieldName = $mappingArray[$fieldName];
        }
        return str_replace('-', '_', strtolower($fieldName));
    }

    /**
     * @param Element $field
     * @param array|null $custom
     * @return bool
     */
    public function canProcess(Element $field, ?array $custom = null): bool
    {
        return in_array($field->type, static::ALLOWED_TYPES_ARRAY);
    }

    /**
     * @param Element     $field
     * @param string|null $language
     * @return string
     */
    protected function getLabel(Element $field, $language): string
    {
        return $this->translator->trans($field->label, [], null, $language);
    }

    /**
     * @param $element
     * @param $arrayMapping
     * @param $name
     * @return string|null
     */
    protected function getThumbnail(Element $element, array $arrayMapping, string $name): ?string
    {
        if (array_key_exists($element->name, $arrayMapping) && in_array($element->type, MapperHelper::IMAGE_TYPES)) {
            foreach ($arrayMapping[$element->name] as $mapping) {
                if (str_replace('-', '_', strtolower($mapping['field'])) === $name) {
                    return $mapping['thumbnail'] ?? AttributeType::IMAGE_DEFAULT;
                }
            }
        }

        return null;
    }
}
