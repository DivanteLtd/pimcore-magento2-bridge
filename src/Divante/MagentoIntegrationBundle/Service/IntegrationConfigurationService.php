<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        19/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service;

use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject;

/**
 * Class IntegrationConfigurationService
 * @package Divante\MagentoIntegrationBundle\Service
 */
class IntegrationConfigurationService
{
    /**
     * @param DataObject\Concrete $object
     * @param string              $instanceUrl
     * @param int                 $storeView
     * @return IntegrationConfiguration
     * @throws \Exception
     */
    public function getFirstConfiguration(
        DataObject\Concrete $object,
        string $instanceUrl,
        int $storeView
    ): IntegrationConfiguration {
        $configurations = $this->getConfigurations($object, $instanceUrl, $storeView);
        if (count($configurations) == 0) {
            throw new \Exception(
                sprintf(
                    '[ERROR] Missing configuration for object: %d, instanceUrl:%s, store view: %d.',
                    $object->getId(),
                    $instanceUrl,
                    $storeView
                )
            );
        }
        return reset($configurations);
    }
    /**
     * @param DataObject\AbstractObject $object
     * @param string         $instanceUrl
     * @param int            $storeView
     * @return array
     */
    protected function getConfigurationConditions(
        DataObject\AbstractObject $object,
        $instanceUrl,
        int $storeView
    ): array {
        if (!$instanceUrl) {
            $condition = "(productClass = :class OR categoryClass = :class)";
            $conditionData = ['class' => $object->getClassId()];
        } else {
            $condition = "instanceUrl = :instance AND magentoStore = :storeView AND " .
                "(productClass = :class OR categoryClass = :class)";
            $conditionData = [
                'instance'  => $instanceUrl,
                'storeView' => $storeView,
                'class'     => $object->getClassId()
            ];
        }
        return ['condition' => $condition, 'data' => $conditionData];
    }

    /**
     * @param DataObject\AbstractObject $object
     * @param string|null         $instanceUrl
     * @param int                 $storeView
     * @return array
     */
    public function getConfigurations(
        DataObject\AbstractObject $object,
        string $instanceUrl = null,
        $storeView = 0
    ): array {
        $conditionData = $this->getConfigurationConditions($object, $instanceUrl, $storeView);
        try {
            $configurationListing = new DataObject\IntegrationConfiguration\Listing();
            $relatedConfigurations = [];
            $configurationListing
                ->setCondition($conditionData['condition'], $conditionData['data'])
                ->load();
        } catch (\Exception $exception) {
            return [];
        }

        foreach ($configurationListing as $configuration) {
            $connectionType = $configuration->getConnectionType($object);
            if ($connectionType >= 0) {
                $relatedConfigurations[] = $configuration;
            }
        }
        return $relatedConfigurations;
    }

    /**
     * @param \stdClass                $object
     * @param IntegrationConfiguration $configuration
     */
    public function setCategoryHierarchyAttribute(\stdClass &$object, IntegrationConfiguration $configuration)
    {
        $object->isRoot = $object->parentId == $configuration->getCategoryRoot()->getId();
    }
}
