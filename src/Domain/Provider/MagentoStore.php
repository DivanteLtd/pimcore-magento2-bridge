<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        12/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Provider;

use Divante\MagentoIntegrationBundle\Application\Integration\Magento\MagentoStoreFetcher;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class MagentoStoreProvider
 * @package Divante\MagentoIntegrationBundle\Provider
 */
class MagentoStore implements SelectOptionsProviderInterface
{
    /** @var MagentoStoreFetcher */
    private $storeFetcher;

    /**
     * MagentoStoreProvider constructor.
     * @param MagentoStoreFetcher $fetcher
     */
    public function __construct(MagentoStoreFetcher $fetcher)
    {
        $this->storeFetcher = $fetcher;
    }

    /**
     * @param array $context
     * @param Data $fieldDefinition
     *
     * @return array
     * @throws NotFoundExceptionInterface
     */
    public function getOptions($context, $fieldDefinition): array
    {
        if (!isset($context['object'])
            || !$context['object'] instanceof IntegrationConfiguration
            || !$context['object']->getInstanceUrl()) {
            return $this->hasNoOption();
        }

        $stores = $this->storeFetcher->getStores($context['object']);

        if (!is_array($stores) || count($stores) == 0) {
            return $this->hasNoOption();
        }
        return array_map(function ($elem) {
            return ["key" => $elem['name'], "value" => $elem['id']];
        }, $stores);
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

    /**
     * @return array
     */
    public function hasNoOption(): array
    {
        return [["key" => "Could not fetch stores", "value" => 0]];
    }
}
