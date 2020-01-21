<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        19/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration;

use Divante\MagentoIntegrationBundle\Domain\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class IntegrationConfigurationService
 * @package Divante\MagentoIntegrationBundle\Service
 */
class IntegrationConfigurationService
{
    /** @var IntegrationConfigurationRepository */
    private $repository;

    /**
     * IntegrationConfigurationService constructor.
     * @param IntegrationConfigurationRepository $repository
     */
    public function __construct(IntegrationConfigurationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Concrete $originObject
     * @param Concrete $object
     * @return array
     */
    public function getConfigurationsListDifference(Concrete $originObject, Concrete $object)
    {
        $originObjectIntegrations = $this->getConfigurations($originObject);
        $objectIntegrations       = $this->getConfigurations($object);
        return array_udiff(
            $originObjectIntegrations,
            $objectIntegrations,
            function ($integration1, $integration2) {
                return $integration1->getId() - $integration2->getId();
            }
        );
    }

    /**
     * @param DataObject\AbstractObject $object
     * @param string                    $relationType
     * @param string|null               $instanceUrl
     * @param int                       $storeView
     * @return array
     */
    public function getConfigurations(
        AbstractObject $object,
        string $relationType = '',
        string $instanceUrl = '',
        $storeView = 0
    ): array {
        $configurations = $this->repository->findConfigurationsByObjectObjectTypeInstanceStoreView(
            $object,
            $instanceUrl,
            $storeView
        );
        if (!$relationType) {
            return $configurations;
        }
        $relatedConfigurations = [];
        foreach ($configurations as $configuration) {
            switch ($relationType) {
                case IntegrationHelper::RELATION_TYPE_CATEGORY:
                    $expectedClassId = $configuration->getCategoryClass();
                    break;
                default:
                    $expectedClassId = $configuration->getProductClass();
                    break;
            }
            $expectedRelationType = $configuration->getRelationType($object);
            if ($expectedRelationType == $relationType && $object->getClassId() == $expectedClassId) {
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
