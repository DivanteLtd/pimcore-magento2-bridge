<?php
/**
 * @category    pimcore
 * @date        26/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\EventListener;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\ValidationException;

/**
 * Class IntegrationConfigurationListener
 * @package Divante\MagentoIntegrationBundle\EventListener
 */
class IntegrationConfigurationListener
{

    /**
     * @param DataObjectEvent $event
     * @throws ValidationException
     */
    public function onPreUpdate(DataObjectEvent $event): void
    {
        /** @var AbstractObject $object */
        $object = $event->getObject();
        if (!$object instanceof IntegrationConfiguration || !$object->isPublished()) {
            return;
        }
        $this->validateConfiguration($object);
        return;
    }

    /**
     * @param IntegrationConfiguration $object
     * @throws ValidationException
     */
    protected function validateConfiguration(IntegrationConfiguration $object): void
    {
        $this->validateMappingConsistency($object);
        $this->validateMappingCorrectness($object);
    }

    /**
     * Validates mapping constency per remote website
     * @param IntegrationConfiguration $object
     * @throws ValidationException
     */
    protected function validateMappingConsistency(IntegrationConfiguration $object): void
    {
        ksort($object->getProductMapping());
        ksort($object->getCategoryMapping());
        $servicePath = $object->getInstanceUrl();
        $sameServiceIntegrations = IntegrationConfiguration::getByInstanceUrl($servicePath);
        foreach ($sameServiceIntegrations as $sameServiceIntegration) {
            if ($sameServiceIntegration->getId() == $object->getId() || !$sameServiceIntegration->isPublished()) {
                continue;
            }
            ksort($sameServiceIntegration->getProductMapping());
            ksort($sameServiceIntegration->getCategoryMapping());
            if ($sameServiceIntegration->getProductMapping() != $object->getProductMapping()
                || $sameServiceIntegration->getCategoryMapping() != $object->getCategoryMapping()
            ) {
                throw new ValidationException(
                    'Mapping for single Magento instance must me this same. '
                    . 'It differs from integration with ID: ' . $sameServiceIntegration->getId()
                );
            }
        }
    }

    /**
     * Validates if all mandatory fields had been filled.
     * @param IntegrationConfiguration $object
     * @throws ValidationException
     */
    protected function validateMappingCorrectness(IntegrationConfiguration $object)
    {
        $mandatoryFields = IntegrationHelper::INTEGRATION_CONFIGURATION_MANDATORY_FIELDS;
        $mapping = $object->getProductMapping();
        $missingFields = [];
        foreach ($mapping as $field) {
            if (($key = array_search($field[1], $mandatoryFields)) !== false) {
                unset($mandatoryFields[$key]);
                if ($field[0] == '') {
                    $missingFields[] = $field[1];
                }
            }
        }
        $incorrectFields =  array_unique(array_merge($missingFields, $mandatoryFields));
        if (count($incorrectFields) > 0) {
            throw new ValidationException(
                "You must fill all mandatory mapping fields. Missing: " . implode(', ', $incorrectFields)
            );
        }
    }
}
