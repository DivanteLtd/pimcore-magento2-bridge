<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        12/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Provider;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

/**
 * Class ClassListProvider
 * @package Divante\MagentoIntegrationBundle\Provider
 */
class ClassList implements SelectOptionsProviderInterface
{

    /**
     * @param array $context
     * @param Data  $fieldDefinition
     *
     * @return array
     */
    public function getOptions($context, $fieldDefinition): array
    {
        $classesList = new ClassDefinition\Listing();
        $classesList->setOrderKey('name');
        $classesList->setOrder('asc');
        $classes = $classesList->load();
        $result  = [];
        foreach ($classes as $class) {
            if ($class === null) {
                continue;
            }
            $result[] = array("key" => $class->getName(), "value" => $class->getId());
        }
        return $result;
    }

    /**
     * @param array $context
     * @param Data  $fieldDefinition
     *
     * @return bool
     */
    public function hasStaticOptions($context, $fieldDefinition): bool
    {
        return false;
    }

    /**
     * @param array $context
     * @param Data  $fieldDefinition
     *
     * @return mixed
     */
    public function getDefaultValue($context, $fieldDefinition)
    {
        return 0;
    }
}
