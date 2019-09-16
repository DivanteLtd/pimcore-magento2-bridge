<?php
/**
 * @category    bosch-stuttgart
 * @date        16/09/2019
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2019 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Provider;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;
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
