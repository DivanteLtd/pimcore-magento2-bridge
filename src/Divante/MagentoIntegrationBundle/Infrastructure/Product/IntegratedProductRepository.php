<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\Product;

use Divante\MagentoIntegrationBundle\Infrastructure\Common\AbstractIntegratedObjectRepository;
use Pimcore\Db;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Class IntegratedProductRepository
 * @package Divante\MagentoIntegrationBundle\Infrastructure\Product
 */
class IntegratedProductRepository extends AbstractIntegratedObjectRepository
{
    /**
     * @param AbstractObject $product
     * @return array
     */
    public function getByRelationObject(AbstractObject $object, int $classId): array
    {
        $configurationClassDef = ClassDefinition::getByName("IntegrationConfiguration");

        return Db::getConnection()->fetchAll("
            SELECT configuration, GROUP_CONCAT(products_ids SEPARATOR ',') as products
            FROM (
                SELECT oConfig.o_id as configuration, o.o_id as products_ids
                FROM object_relations_" . $classId . " as rel
                JOIN objects as o ON o.o_id = rel.src_id
                INNER JOIN object_" . $configurationClassDef->getId() . " as oConfig
                    ON o.o_path
                    LIKE CONCAT('%',oConfig.productRootPath,'%')
                WHERE rel.dest_id = " . $object->getId() . "
                GROUP BY products_ids
        ) o
        GROUP BY configuration
        ");
    }

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
