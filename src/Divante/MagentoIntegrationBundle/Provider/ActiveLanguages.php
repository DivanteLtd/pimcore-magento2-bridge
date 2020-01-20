<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Provider;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Pimcore\Tool;

/**
 * Class ActiveLanguages
 * @package Divante\MagentoIntegrationBundle\Provider
 */
class ActiveLanguages implements SelectOptionsProviderInterface
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
        return array_map(
            function ($language) {
                return ['key' => $language, 'value' => $language];
            },
            Tool::getValidLanguages()
        );
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
        return 'en';
    }
}
