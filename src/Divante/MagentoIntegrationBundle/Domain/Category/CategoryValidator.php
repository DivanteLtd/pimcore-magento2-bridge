<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Domain\Category;

use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\ValidationException;

/**
 * Class CategoryValidator
 * @package Divante\MagentoIntegrationBundle\Domain\Category
 */
class CategoryValidator
{
    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     * @throws ValidationException
     */
    public function validate(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $parent = $element->getParent();
        $categoryRootId =$configuration->getCategoryRoot()->getId();
        while ($parent->getId() != $categoryRootId & $parent->getId() != 1) {
            if ($parent instanceof Concrete && !$parent->isPublished()) {
                throw new ValidationException("All parent categories must be before publishing children.");
            }
            $parent = $parent->getParent();
        }
    }
}
