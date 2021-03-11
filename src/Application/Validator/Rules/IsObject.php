<?php


namespace Divante\MagentoIntegrationBundle\Application\Validator\Rules;

use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\ValidationException;

/**
 * Class IsObject
 * @package Divante\MagentoIntegrationBundle\Application\Validator\Rules
 */
class IsObject implements ObjectValidationRuleInterface
{

    /**
     * @inheritDoc
     */
    public function isValid(AbstractObject $object, IntegrationConfiguration $configuration, string $type)
    {
        if ($object->getType() != AbstractObject::OBJECT_TYPE_OBJECT
            && $object->getType() != AbstractObject::OBJECT_TYPE_VARIANT
        ) {
            throw new ValidationException("Element is not a valid object");
        }
    }

    /**
     * @inheritDoc
     */
    public static function getPriority(): string
    {
        return "10";
    }

    /**
     * @inheritDoc
     */
    public function isSilent(): bool
    {
        return false;
    }
}
