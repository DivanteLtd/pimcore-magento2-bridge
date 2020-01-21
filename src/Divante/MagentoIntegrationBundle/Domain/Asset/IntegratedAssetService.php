<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Asset;

use Divante\MagentoIntegrationBundle\Domain\Common\AbstractIntegratedObjectService;
use Divante\MagentoIntegrationBundle\Domain\Common\StatusService;
use Divante\MagentoIntegrationBundle\Domain\Helper\ObjectStatusHelper;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationConfigurationService;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Rest\RestClientBuilder;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Listing\AbstractListing;

/**
 * Class IntegratedAssetService
 * @package Divante\MagentoIntegrationBundle\Domain\Asset
 */
class IntegratedAssetService extends AbstractIntegratedObjectService
{
    /** @var IntegrationConfigurationService */
    private $configService;

    /**
     * IntegratedCategoryService constructor.
     * @param RestClientBuilder $builder
     */
    public function __construct(
        StatusService $statusService,
        RestClientBuilder $builder,
        IntegrationConfigurationService $configurationService
    ) {
        parent::__construct($statusService, $builder);
        $this->configService = $configurationService;
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function send(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->builder->getClient($configuration)->sendModifiedAsset($element);
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function delete(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->builder->getClient($configuration)->deleteCategory($element);
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     * @return bool
     */
    public function supports(AbstractElement $element, IntegrationConfiguration $configuration): bool
    {
        return $element instanceof Asset;
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function validate(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    public function getDependentEndpoints(AbstractElement $element): array
    {
        if (!$element) {
            return [];
        }
        /** @var DataObject\Listing $objectListing */
        $objectListing     = $this->getDependentElementsListing($element);
        $endpointsToNotify = [];
        if (!$objectListing instanceof DataObject\Listing) {
            return $endpointsToNotify;
        }
        /** @var DataObject\Concrete $related */
        foreach ($objectListing as $relatedObject) {
            if ($relatedObject->getProperty(ObjectStatusHelper::SYNC_PROPERTY_NAME) == null) {
                continue;
            }
            try {
                $configurationListing = $this->configService->getConfigurations($relatedObject);
                foreach ($configurationListing as $configuration) {
                    $endpointsToNotify[$configuration->getId()] = $configuration;
                }
            } catch (\Exception $exception) {
                continue;
            }
        }
        return $endpointsToNotify;
    }

    /**
     * @param AbstractElement $element
     * @return AbstractListing|null
     */
    protected function getDependentElementsListing(AbstractElement $element): ?AbstractListing
    {
        $listing = new DataObject\Listing();
        $ids     = array_map(
            function ($element) {
                return $element['id'];
            },
            array_filter(
                $element->getDependencies()->getRequiredBy(),
                function ($elem) {
                    return $elem['type'] == 'object';
                }
            )
        );
        if (!$ids) {
            return null;
        }
        $inQuery = implode(',', array_fill(0, count($ids), '?'));
        $listing->setCondition('o_id IN (' . $inQuery . ')', $ids);
        $listing->load();

        return $listing;
    }
}
