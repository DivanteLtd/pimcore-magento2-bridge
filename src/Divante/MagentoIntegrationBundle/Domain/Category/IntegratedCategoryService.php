<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Domain\Category;

use Divante\MagentoIntegrationBundle\Domain\Common\AbstractIntegratedObjectService;
use Divante\MagentoIntegrationBundle\Domain\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Domain\Common\StatusService;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Rest\RestClientBuilder;
use Divante\MagentoIntegrationBundle\Domain\DataObject\DataObjectEventListener;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\ValidationException;

/**
 * Class RemoteCategoryService
 * @package Divante\MagentoIntegrationBundle\Domain\Category
 */
class IntegratedCategoryService extends AbstractIntegratedObjectService
{
    /**
     * @var RestClientBuilder
     */
    private $builder;
    /**
     * @var CategoryValidator
     */
    private $validator;

    /**
     * IntegratedCategoryService constructor.
     * @param StatusService     $statusService
     * @param RestClientBuilder $builder
     * @param CategoryValidator $validator
     */
    public function __construct(StatusService $statusService, RestClientBuilder $builder, CategoryValidator $validator)
    {
        parent::__construct($statusService);
        $this->builder = $builder;
        $this->validator = $validator;
    }

    public function send(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->builder->getClient($configuration)->sendCategory($element);
    }
    public function delete(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->builder->getClient($configuration)->deleteCategory($element);
    }

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
