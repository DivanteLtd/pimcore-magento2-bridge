<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        06/04/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Provider;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;

/**
 * Class ClassificationStore
 * @package Divante\MagentoIntegrationBundle\Provider
 */
class ClassificationStore implements SelectOptionsProviderInterface
{
    /**
     * @param array $context
     * @param Data $fieldDefinition
     *
     * @return array
     * @throws \Exception
     */
    public function getOptions($context, $fieldDefinition): array
    {
        $stores = new StoreConfig\Listing();
        $stores->load();
        $data = [];
        /** @var StoreConfig $store */
        foreach ($stores->getList() as $store) {
            $data[] =  ["key" => $store->getName(), "value" => $store->getId()];
        }
        return $data;
    }

    /**
     * @param array $context
     * @param Data $fieldDefinition
     *
     * @return bool
     */
    public function hasStaticOptions($context, $fieldDefinition): bool
    {
        return false;
    }

    /**
     * @param array $context
     * @param Data $fieldDefinition
     *
     * @return mixed
     */
    public function getDefaultValue($context, $fieldDefinition)
    {
        return 0;
    }
}
