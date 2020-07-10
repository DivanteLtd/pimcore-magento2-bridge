<?php

namespace Divante\MagentoIntegrationBundle\Application\Validator\Rules\Category;

use Divante\MagentoIntegrationBundle\Application\Validator\Rules\ObjectValidationRuleInterface;
use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\ValidationException;

/**
 * Class CategoryParentsMustBePublished
 * @package Divante\MagentoIntegrationBundle\Application\Validator\Rules\Category
 */
class CategoryParentsMustBePublished implements ObjectValidationRuleInterface
{

    /**
     * @inheritDoc
     */
    public function isValid(AbstractObject $object, IntegrationConfiguration $configuration, string $type)
    {
        if ($type != ObjectTypeHelper::CATEGORY) {
            return;
        }
        $parent         = $object->getParent();
        $categoryRootId = $configuration->getCategoryRoot()->getId();
        while ($parent->getId() != $categoryRootId & $parent->getId() != 1) {
            if ($parent instanceof Concrete && !$parent->isPublished()) {
                throw new ValidationException("All parent categories must be before publishing children.");
            }
            $parent = $parent->getParent();
        }
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
