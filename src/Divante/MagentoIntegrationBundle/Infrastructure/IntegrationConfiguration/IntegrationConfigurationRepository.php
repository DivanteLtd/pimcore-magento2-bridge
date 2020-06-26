<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Factory;

/**
 * Class IntegrationConfigurationRepository
 * @package Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration
 */
class IntegrationConfigurationRepository
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * IntegrationConfigurationRepository constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return \Pimcore\Model\AbstractModel|\Pimcore\Model\Listing\AbstractListing
     */
    private function getOrderedListing()
    {
        $listing = $this->factory->build(IntegrationConfiguration\Listing::class);
        $listing->setOrderKey('o_index');
        $listing->setOrder('asc');

        return $listing;
    }

    /**
     * @return IntegrationConfiguration[]
     */
    public function getAllConfigurations(): array
    {
        return $this->getOrderedListing()->load();
    }

    /**
     * @param AbstractObject $object
     * @return IntegrationConfiguration[]
     */
    public function getByProduct(AbstractObject $object): array
    {
        return $this->getOrderedListing()
            ->setCondition(
                ":path LIKE CONCAT('%', productRootPath, '%')",
                ['path' => $object->getPath()]
            )->load();
    }

    /**
     * @param AbstractObject $object
     * @return IntegrationConfiguration[]
     */
    public function getByCategory(AbstractObject $object): array
    {
        return $this->getOrderedListing()
            ->setCondition(
                ":path LIKE CONCAT('%', categoryRootPath, '%')",
                ['path' => $object->getPath()]
            )->load();
    }

    /**
     * @param array $integrationIds
     * @return array
     * @throws \Exception
     */
    public function getByIntegrationIds(array $integrationIds): array
    {
        $configurationListing = $this->getOrderedListing();
        $configurationListing
            ->setCondition("integrationId IN (?)", [$integrationIds])
            ->load();
        return $configurationListing->getObjects();
    }


    /**
     * @param AbstractObject $object
     * @param string         $instanceUrl
     * @param int            $storeView
     * @return array|IntegrationConfiguration\Listing
     */
    public function findConfigurationsByObjectObjectTypeInstanceStoreView(
        AbstractObject $object,
        string $instanceUrl,
        int $storeView
    ) {
        $conditionData = $this->getConfigurationConditions($instanceUrl, $storeView, $object);
        try {
            $configurationListing = $this->getOrderedListing();
            $configurationListing
                ->setCondition($conditionData['condition'], $conditionData['data'])
                ->load();
        } catch (\Exception $exception) {
            return [];
        }
        return $configurationListing->getObjects();
    }

    /**
     * @param string              $instanceUrl
     * @param int                 $storeView
     * @param AbstractObject|null $object
     * @return array
     */
    public function getByConfiguration(
        string $instanceUrl,
        int $storeView,
        ?AbstractObject $object = null
    ): array {
        $conditionData = $this->getConfigurationConditions($instanceUrl, $storeView, $object);
        try {
            $configurationListing = $this->getOrderedListing();
            $configurationListing
                ->setCondition($conditionData['condition'], $conditionData['data'])
                ->load();
        } catch (\Exception $exception) {
            return [];
        }
        return $configurationListing->getObjects();
    }

    /**
     * @return array
     */
    public function getAllProductClasses(): array
    {
        $listing = $this->getOrderedListing();
        $productClasses = [];
        /** @var IntegrationConfiguration $object */
        foreach ($listing->getData() as $object) {
            if ($object->getProductClass()) {
                $productClasses[] = $object->getProductClass();
            }
        }

        return array_unique($productClasses);
    }

    /**
     * @return array
     */
    public function getAllCategoryClasses(): array
    {
        $listing = $this->getOrderedListing();
        $categoryClasses = [];
        /** @var IntegrationConfiguration $object */
        foreach ($listing->getData() as $object) {
            if ($object->getCategoryClass()) {
                $categoryClasses[] = $object->getCategoryClass();
            }
        }

        return array_unique($categoryClasses);
    }

    /**
     * @param AbstractObject $object
     * @param string         $instanceUrl
     * @param int            $storeView
     * @return array
     */
    protected function getConfigurationConditions(
        $instanceUrl,
        int $storeView,
        ?AbstractObject $object
    ): array {
        if (!$instanceUrl) {
            $condition     = "(productClass = :class OR categoryClass = :class)";
            $conditionData = ['class' => $object->getClassId()];
        } else {
            $condition     = "instanceUrl = :instance AND magentoStore = :storeView";
            $conditionData = [
                'instance'  => $instanceUrl,
                'storeView' => $storeView,
            ];
        }
        return ['condition' => $condition, 'data' => $conditionData];
    }
}
