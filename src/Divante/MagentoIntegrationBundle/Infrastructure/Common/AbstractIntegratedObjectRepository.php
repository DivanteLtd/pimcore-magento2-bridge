<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\Common;

use Divante\MagentoIntegrationBundle\Application\Common\IntegratedObjectRepositoryInterface;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;

/**
 * Class AbstractIntegratedObjectRepository
 * @package Divante\MagentoIntegrationBundle\Infrastructure\Common
 */
abstract class AbstractIntegratedObjectRepository implements IntegratedObjectRepositoryInterface
{
    /**
     * @param IntegrationConfiguration $configuration
     * @param array|null $objectsId
     * @return array
     * @throws \Exception
     */
    abstract protected function getObjectConditions(
        IntegrationConfiguration $configuration,
        ?array $objectsId = null
    ) : array;

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    abstract protected function getObjectsRoot(IntegrationConfiguration $configuration): string;

    /**
     * @param IntegrationConfiguration $configuration
     * @param string $type
     * @return array
     * @throws \Exception
     */
    public function getAllObjects(IntegrationConfiguration $configuration): array
    {
        AbstractObject::setHideUnpublished(false);
        $listing = new Listing();
        list($conditions, $params) = $this->getObjectConditions($configuration);
        $listing->setCondition(
            $conditions . " AND o_path LIKE :path",
            array_merge(
                $params,
                [
                    "path" => sprintf("%s", $this->getObjectsRoot($configuration) . "%"),
                ]
            )
        );
        $listing->setObjectTypes([AbstractObject::OBJECT_TYPE_VARIANT, AbstractObject::OBJECT_TYPE_OBJECT]);
        return $listing->getObjects();
    }

    /**
     * @param array $objectsId
     * @param IntegrationConfiguration $configuration
     * @param string $type
     * @return Concrete[]
     * @throws \Exception
     */
    public function getObjects(array $objectsId, IntegrationConfiguration $configuration): array
    {
        $listing = new Listing();
        list($conditions, $params) = $this->getObjectConditions($configuration, $objectsId);
        $listing->setCondition(
            $conditions . " AND o_path LIKE :path",
            array_merge(
                $params,
                [
                    "path" => sprintf("%s", $this->getObjectsRoot($configuration) . "%"),
                ]
            )
        );
        $listing->setObjectTypes([AbstractObject::OBJECT_TYPE_VARIANT, AbstractObject::OBJECT_TYPE_OBJECT]);
        return $listing->getObjects();
    }
}
