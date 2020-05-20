<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\Category;

use Divante\MagentoIntegrationBundle\Infrastructure\Common\AbstractIntegratedObjectRepository;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Class IntegratedCategoryRepository
 */
class IntegratedCategoryRepository extends AbstractIntegratedObjectRepository
{

    /**
     * @inheritDoc
     */
    protected function getObjectConditions(IntegrationConfiguration $configuration, ?array $objectsId = null): array
    {
        $class = ClassDefinition::getById($configuration->getCategoryClass());
        $condition = 'o_className = :className';
        $params = [
            'className' => $class->getName(),
        ];
        if ($objectsId) {
            $condition .= " AND o_id IN (" . implode(',', array_map('intval', $objectsId)) . ")";
        }

        return [
            $condition,
            $params
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getObjectsRoot(IntegrationConfiguration $configuration): string
    {
        return $configuration->getCategoryRootPath() ?? "";
    }
}
