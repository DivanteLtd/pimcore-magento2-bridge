<?php
/**
 * @category    bosch-stuttgart
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Class IntegrationConfigurationRepository
 * @package Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration
 */
class IntegrationConfigurationRepository
{
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
        $conditionData = $this->getConfigurationConditions($object, $instanceUrl, $storeView);
        try {
            $configurationListing = new IntegrationConfiguration\Listing();
            $configurationListing
                ->setCondition($conditionData['condition'], $conditionData['data'])
                ->load();
        } catch (\Exception $exception) {
            return [];
        }
        return $configurationListing->getObjects();
    }

    /**
     * @param AbstractObject $object
     * @param string         $instanceUrl
     * @param int            $storeView
     * @return array
     */
    protected function getConfigurationConditions(
        AbstractObject $object,
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
}
