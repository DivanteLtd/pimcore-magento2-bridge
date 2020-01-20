<?php
/**
 * @category    bosch-stuttgart
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration;

use Divante\MagentoIntegrationBundle\Domain\Helper\IntegrationHelper;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\ValidationException;

/**
 * Class IntegrationConfigurationValidationService
 * @package Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration
 */
class IntegrationConfigurationValidator
{
    /**
     * @param IntegrationConfiguration $object
     * @throws ValidationException
     */
    public function validate(IntegrationConfiguration $object): void
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
        $mandatoryFieldsProduct  = IntegrationHelper::INTEGRATION_CONFIGURATION_MANDATORY_FIELDS_PRODUCT;
        $mandatoryFieldsCategory = IntegrationHelper::INTEGRATION_CONFIGURATION_MANDATORY_FIELDS_CATEGORY;
        $productMapping          = $object->getProductMapping();
        $missingFieldsProduct    = [];
        foreach ($productMapping as $field) {
            if (($key = array_search($field[1], $mandatoryFieldsProduct)) !== false) {
                unset($mandatoryFieldsProduct[$key]);
                if ($field[0] == '') {
                    $missingFieldsProduct[] = $field[1];
                }
            }
        }
        $categoryMapping       = $object->getCategoryMapping();
        $missingFieldsCategory = [];
        foreach ($categoryMapping as $field) {
            if (($key = array_search($field[1], $mandatoryFieldsCategory)) !== false) {
                unset($mandatoryFieldsCategory[$key]);
                if ($field[0] == '') {
                    $missingFieldsCategory[] = $field[1];
                }
            }
        }
        $incorrectFieldsProduct  = array_unique(array_merge($missingFieldsProduct, $mandatoryFieldsProduct));
        $incorrectFieldsCategory = array_unique(array_merge($missingFieldsCategory, $mandatoryFieldsCategory));
        $errorMsg                = '';
        if (count($incorrectFieldsProduct) > 0) {
            $errorMsg .= '<br/> product mapping: ' . implode(', ', $incorrectFieldsProduct);
        }
        if (count($incorrectFieldsCategory) > 0) {
            $errorMsg .= "<br/> category mapping: " . implode(', ', $incorrectFieldsCategory);
        }
        if ($errorMsg) {
            throw new ValidationException(
                "You must fill all mandatory mapping fields. Missing fields " . $errorMsg
            );
        }
    }
}
