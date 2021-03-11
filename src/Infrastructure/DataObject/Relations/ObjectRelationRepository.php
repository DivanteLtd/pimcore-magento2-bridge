<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\DataObject\Relations;

use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Pimcore\Db;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;

/**
 * Class ObjectRelationRepository
 * @package Divante\MagentoIntegrationBundle\Infrastructure\DataObject\Relations
 */
class ObjectRelationRepository
{
    /**
     * @param AbstractObject $object
     * @param string $classId
     * @param string $type
     * @return array
     */
    public function getByRelationObject(AbstractObject $object, string $classId, string $type): array
    {
        $configurationClassDef = ClassDefinition::getByName("IntegrationConfiguration");
        switch ($type) {
            case ObjectTypeHelper::PRODUCT:
                $objectRootPath = "productRootPath";
                break;
            case ObjectTypeHelper::CATEGORY:
                $objectRootPath = "categoryRootPath";
                break;
            default:
                throw new \InvalidArgumentException("Invalid type: " . $type);
        }

        return Db::getConnection()->fetchAll("
            SELECT configuration, GROUP_CONCAT(objects_ids SEPARATOR ',') as objectIds
            FROM (
                SELECT oConfig.o_id as configuration, o.o_id as objects_ids
                FROM object_relations_" . $classId . " as rel
                JOIN objects as o ON o.o_id = rel.src_id
                INNER JOIN object_" . $configurationClassDef->getId() . " as oConfig
                    ON o.o_path
                    LIKE CONCAT('%',oConfig." . $objectRootPath . ",'%')
                WHERE rel.dest_id = " . $object->getId() . "
                GROUP BY objects_ids
        ) o
        GROUP BY configuration
        ");
    }

    /**
     * @param array $productClasses
     * @param array $categoryClasses
     * @return array
     * @throws \Exception
     */
    public function getAllPossibleRelationClassesNames(array $productClasses, array $categoryClasses): array
    {
        $classesList = new ClassDefinition\Listing();
        $classes = array_map(function ($classDef) {
            return $classDef->getName();
        }, $classesList->load());

        foreach ($productClasses as $classId) {
            $productClass = ClassDefinition::getById($classId);
            $index = array_search($productClass->getName(), $classes);
            unset($classes[$index]);
        }
        foreach ($categoryClasses as $classId) {
            $categoryClass = ClassDefinition::getById($classId);
            $index = array_search($categoryClass->getName(), $classes);
            unset($classes[$index]);
        }

        return $classes;
    }
}
