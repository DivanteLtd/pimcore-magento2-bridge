<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractMapStrategy
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
abstract class AbstractMapKeyStrategy implements MapKeyStrategyInterface
{
    const ALLOWED_TYPES_ARRAY = [];
    /** @var TranslatorInterface  */
    protected $translator;

    /**
     * @param KeyConfig   $field
     * @param array       $attribute
     * @param array       $group
     * @param \stdClass   $obj
     * @param array       $arrayMapping
     * @param string|null $language
     * @return void
     */
    abstract public function map(
        KeyConfig $field,
        array $attribute,
        array $group,
        \stdClass &$obj,
        array $arrayMapping,
        $language
    ): void;

    /**
     * AbstractMapKeyStrategy constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param KeyConfig $field
     * @return bool
     */
    public function canProcess(KeyConfig $field): bool
    {
        return in_array($field->type, static::ALLOWED_TYPES_ARRAY);
    }

    /**
     * @param string $fieldName
     * @param array  $mappingArray
     * @return array
     */
    public function mapStringNames(string $fieldName, string $groupName, array $mappingArray): array
    {
        $name = $groupName . $fieldName;
        if (array_key_exists($name, $mappingArray)) {
            $names = $mappingArray[$name];
        } else {
            $names = [$name];
        }
        return str_replace(' ', '', str_replace('-', '_', array_map('strtolower', $names)));
    }

    /**
     * @param string $label
     * @param string $language
     * @return string
     */
    protected function getLabel($label, string $language): string
    {
        return $language ? $this->translator->trans($label, [], null, $language) : $label;
    }
}
