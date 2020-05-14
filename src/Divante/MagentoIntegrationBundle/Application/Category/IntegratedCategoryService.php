<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Category;

use Divante\MagentoIntegrationBundle\Application\Category\CategoryValidator;
use Divante\MagentoIntegrationBundle\Application\Common\AbstractIntegratedObjectService;
use Divante\MagentoIntegrationBundle\Application\Common\StatusService;
use Divante\MagentoIntegrationBundle\Infrastructure\DataObject\DataObjectEventListener;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Rest\RestClientBuilder;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\ValidationException;

/**
 * Class RemoteCategoryService
 * @package Divante\MagentoIntegrationBundle\Domain\Category
 */
class IntegratedCategoryService extends AbstractIntegratedObjectService
{
    /** @var CategoryValidator */
    private $validator;

    /**
     * IntegratedCategoryService constructor.
     * @param StatusService     $statusService
     * @param RestClientBuilder $builder
     * @param CategoryValidator $validator
     */
    public function __construct(StatusService $statusService, RestClientBuilder $builder, CategoryValidator $validator)
    {
        parent::__construct($statusService, $builder);
        $this->validator = $validator;
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function send(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->builder->getClient($configuration)->sendCategory($element);
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
        return $configuration->getRelationType($element) == IntegrationHelper::RELATION_TYPE_CATEGORY;
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     * @throws ValidationException
     */
    public function validate(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        if (!$element->isPublished()) {
            return;
        }
        $this->validator->validate($element, $configuration);
        if ($this->isOnlyIndexChanged($element)) {
            $this->removeIntegratorListeners(DataObjectEventListener::class);
        }
    }
}
