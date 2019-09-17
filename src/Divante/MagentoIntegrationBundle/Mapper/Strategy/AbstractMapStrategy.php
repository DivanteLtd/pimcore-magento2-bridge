<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Pimcore\Model\Webservice\Data\DataObject\Element;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractMapStrategy
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
abstract class AbstractMapStrategy implements MapStrategyInterface
{
    const ALLOWED_TYPES_ARRAY = [];

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
        } else {
            $fieldName = [$field->name];
        }
        return str_replace('-', '_', array_map('strtolower', $fieldName));
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
     * @return bool
     */
    public function canProcess(Element $field): bool
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
}
