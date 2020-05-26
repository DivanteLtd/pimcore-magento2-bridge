<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\Product;

use Divante\MagentoIntegrationBundle\Infrastructure\Common\AbstractIntegratedObjectRepository;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Class IntegratedProductRepository
 * @package Divante\MagentoIntegrationBundle\Infrastructure\Product
 */
class IntegratedProductRepository extends AbstractIntegratedObjectRepository
{
    /**
     * @inheritDoc
     */
    protected function getObjectConditions(
        IntegrationConfiguration $configuration,
        ?array $objectsId = null
    ): array {
        $class = ClassDefinition::getById($configuration->getProductClass());
        $condition = 'o_className = :className';
        $params = [
            'className' => $class->getName(),
        ];
        if ($objectsId) {
            $condition .= " AND o_id IN (" . implode(',', array_map('intval', $objectsId)) . ")";
        }

        return [$condition, $params];
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    protected function getObjectsRoot(IntegrationConfiguration $configuration): string
    {
        return $configuration->getProductRootPath() ?? "";
    }
}
