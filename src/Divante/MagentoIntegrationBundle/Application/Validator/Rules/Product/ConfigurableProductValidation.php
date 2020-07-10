<?php

namespace Divante\MagentoIntegrationBundle\Application\Validator\Rules\Product;

use Divante\MagentoIntegrationBundle\Application\Validator\Rules\ObjectValidationRuleInterface;
use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperHelper;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\ValidationException;

/**
 * Class ConfigurableProductValidation
 * @package Divante\MagentoIntegrationBundle\Application\Validator\Rules\Product
 */
class ConfigurableProductValidation implements ObjectValidationRuleInterface
{

    /**
     * @inheritDoc
     */
    public function isValid(AbstractObject $object, IntegrationConfiguration $configuration, string $type)
    {
        if ($type !== ObjectTypeHelper::PRODUCT) {
            return;
        }
        if (!$object->isPublished()) {
            return;
        }
        AbstractObject::setHideUnpublished(true);
        $children = $object->getChildren(
            [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]
        );
        if (count($children) > 0 && !$object->hasProperty(IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE)) {
            throw new ValidationException(
                "Configurable product cannot be published without specified configurable attribute. Add property: "
                . IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE . "."
            );
        } elseif ($object->getParent() instanceof Concrete) {
            if ($object->getParent()->isPublished() == false) {
                throw new ValidationException(
                    "To publish a variant its parent must be published."
                );
            }
            $urlKeyAttrName = $this->getUrlKeyAttrName($configuration);
            if ($urlKeyAttrName) {
                $elementUrlKey = $object->get($urlKeyAttrName);
                if (!$elementUrlKey || $object->getParent()->get($urlKeyAttrName) == $elementUrlKey) {
                    throw new ValidationException('Variant must have unique attribute urlKey!');
                }
            }
            $this->checkConfigurableAttributesValues($object);
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

    /**
     * @param IntegrationConfiguration $configuration
     * @return string|null
     */
    protected function getUrlKeyAttrName(IntegrationConfiguration $configuration): ?string
    {
        $mappings = $configuration->getDecodedProductMapping();
        foreach ($mappings as  $key => $mapping) {
            foreach ($mapping as $element) {
                if ($element['field'] === MapperHelper::VALUE_KEY_URL_KEY) {
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public static function getPriority(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function isSilent(): bool
    {
        return false;
    }
}
