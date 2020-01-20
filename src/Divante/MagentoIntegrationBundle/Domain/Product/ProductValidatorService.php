<?php
/**
 * @category    bosch-stuttgart
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Product;

use Divante\MagentoIntegrationBundle\Domain\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Domain\Helper\MapperHelper;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ValidationException;

/**
 * Class ProductValidatorService
 * @package Divante\MagentoIntegrationBundle\Domain\Product
 */
class ProductValidatorService
{
    /**
     * Configurable products cannot be published without published variants
     * Configurable product cannot be published without specified configurable attributes
     * All products must have unique url_key
     * @param AbstractObject            $element
     * @param IntegrationConfiguration $configuration
     * @throws ValidationException
     */
    public function validateProduct(AbstractObject $element, IntegrationConfiguration $configuration): void
    {
        if (!$element->isPublished()) {
            return;
        }
        AbstractObject::setHideUnpublished(true);
        $children = $element->getChildren(
            [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]
        );
        if (count($children) > 0 && !$element->hasProperty(IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE)) {
            throw new ValidationException(
                "Configurable product cannot be published without specified configurable attribute. Add property: "
                . IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE . "."
            );
        } elseif ($element->getParent() instanceof Concrete) {
            if ($element->getParent()->isPublished() == false) {
                throw new ValidationException(
                    "To publish a variant its parent must be published."
                );
            }
            $mapping        = $configuration->getDecodedProductMapping();
            $urlKeyAttrName = array_search(MapperHelper::VALUE_KEY_URL_KEY, $mapping);
            if ($urlKeyAttrName) {
                $elementUrlKey = $element->get($urlKeyAttrName);
                if (!$elementUrlKey || $element->getParent()->get($urlKeyAttrName) == $elementUrlKey) {
                    throw new ValidationException('Variant must have unique attribute urlKey!');
                }
            }
            $this->checkConfigurableAttributesValues($element);
        }
    }

    /**
     * @param AbstractObject $object
     * @throws ValidationException
     */
    protected function checkConfigurableAttributesValues(AbstractObject $object): void
    {
        $configAttributes = $object->getProperty(IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE);
        $attributes       = explode(',', $configAttributes);
        foreach ($attributes as $attribute) {
            if (!$attribute) {
                continue;
            }
            if (strpos($attribute, '_')) {
                $attrNameArray = explode('_', $attribute);
                if (property_exists(get_class($object), $attrNameArray[0])) {
                    continue;
                }
            }
            $value = $object->get($attribute);
            if ($value == null) {
                throw new ValidationException('Missing value for configurable attribute: ' . $attribute);
            }
        }
    }
}
