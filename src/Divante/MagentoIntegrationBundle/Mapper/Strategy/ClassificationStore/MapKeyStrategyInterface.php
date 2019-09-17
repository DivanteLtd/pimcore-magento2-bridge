<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

/**
 * Interface MapKeyStrategyInterface
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
interface MapKeyStrategyInterface
{
    /**
     * @param KeyConfig   $field
     * @param array       $attribute
     * @param array       $group
     * @param \stdClass   $obj
     * @param array       $arrayMapping
     * @param string|null $language
     * @return void
     */
    public function map(
        KeyConfig $field,
        array $attribute,
        array $group,
        \stdClass &$obj,
        array $arrayMapping,
        $language
    ): void;

    /**
     * @param KeyConfig $field
     * @return bool
     */
    public function canProcess(KeyConfig $field): bool;

    /**
     * @param string $fieldName
     * @param string $groupName
     * @param array  $mappingArray
     * @return array
     */
    public function mapStringNames(string $fieldName, string $groupName, array $mappingArray): array;

}
