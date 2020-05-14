<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Product;

use Divante\MagentoIntegrationBundle\Application\Common\AbstractIntegratedObjectService;
use Divante\MagentoIntegrationBundle\Application\Common\StatusService;
use Divante\MagentoIntegrationBundle\Infrastructure\DataObject\DataObjectEventListener;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Application\Product\ProductValidatorService;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Rest\RestClientBuilder;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\ValidationException;

/**
 * Class RemoteProductService
 * @package Divante\MagentoIntegrationBundle\Domain\Product
 */
class IntegratedProductService extends AbstractIntegratedObjectService
{
    /** @var ProductValidatorService */
    private $validator;

    /**
     * IntegratedProductService constructor.
     * @param StatusService           $statusService
     * @param RestClientBuilder       $builder
     * @param ProductValidatorService $validator
     */
    public function __construct(
        StatusService $statusService,
        RestClientBuilder $builder,
        ProductValidatorService $validator
    ) {
        parent::__construct($statusService, $builder);
        $this->validator = $validator;
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function send(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->builder->getClient($configuration)->sendProduct($element);
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function delete(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->builder->getClient($configuration)->deleteProduct($element);
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     * @return bool
     */
    public function supports(AbstractElement $element, IntegrationConfiguration $configuration): bool
    {
        return $configuration->getRelationType($element) == IntegrationHelper::RELATION_TYPE_PRODUCT;
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     * @throws ValidationException
     */
    public function validate(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->validator->validateProduct($element, $configuration);
        if ($this->isOnlyIndexChanged($element)) {
            $this->removeIntegratorListeners(DataObjectEventListener::class);
        }
    }
}
