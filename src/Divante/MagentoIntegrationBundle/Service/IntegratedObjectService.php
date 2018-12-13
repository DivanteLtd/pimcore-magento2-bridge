<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        01/06/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Listing\AbstractListing;

/**
 * Class IntegratedObjectService
 * @package Divante\MagentoIntegrationBundle\Service
 */
class IntegratedObjectService
{
    /** @var IntegrationConfigurationService */
    protected $integrationService;

    /**
     * IntegratedObjectService constructor.
     * @param IntegrationConfigurationService $integrationService
     */
    public function __construct(IntegrationConfigurationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    public function getDependentEndpoints(AbstractElement $element): array
    {
        /** @var DataObject\Listing $objectListing */
        $objectListing = $this->getDependentElementsListing($element, 'object');
        $endpointsToNotify = [];
        if (!$objectListing instanceof DataObject\Listing) {
            return $endpointsToNotify;
        }
        /** @var DataObject\Concrete $related */
        foreach ($objectListing as $relatedObject) {
            if ($relatedObject->getProperty('synchronize-status') == null) {
                continue;
            }
            try {
                $configurationListing = $this->integrationService->getConfigurations($relatedObject);
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
     * @param string          $elementType
     * @return null|AbstractListing
     */
    protected function getDependentElementsListing(AbstractElement $element, $elementType = 'object')
    {
        $listing = $this->getListingInstance($elementType);
        if (!$listing instanceof AbstractListing) {
            return null;
        }
        $ids = $this->getDependentElementsIds($element);
        if (!array_key_exists($elementType, $ids) || count($ids[$elementType]) == 0) {
            return null;
        }
        $ids = $ids[$elementType];
        $inQuery       = implode(',', array_fill(0, count($ids), '?'));
        $listing->setCondition('o_id IN (' . $inQuery . ')', $ids);
        $listing->load();

        return $listing;
    }

    /**
     * @param $elementType
     * @return AbstractListing
     */
    protected function getListingInstance($elementType): AbstractListing
    {
        /** @var AbstractListing $listing */
        $listing = null;

        if (!$elementType) {
            return null;
        }
        switch ($elementType) {
            case ('object'):
                $listing = new DataObject\Listing();
                break;
            case ('asset'):
                $listing = new Asset\Listing();
                break;
            case ('document'):
                $listing =  new Document\Listing();
                break;
            default:
                $elementType = ucfirst(trim(preg_replace('/\\0/', '', $elementType)));
                $className = 'Pimcore\\Model\\' . $elementType . '\\Listing';
                if (class_exists($className)) {
                    $listing = new $className();
                } else {
                    return null;
                }
        }
        return $listing;
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    protected function getDependentElementsIds(AbstractElement $element): array
    {
        if (!$element instanceof DataObject\Concrete) {
            return [];
        }
        $dependencies = [];

        foreach ($element->getDependencies()->getRequiredBy() as $dependency) {
            $dependencies[$dependency['type']][] = $dependency['id'];
        }
        return $dependencies;
    }
}
