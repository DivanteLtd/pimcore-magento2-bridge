<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Category;

use Divante\MagentoIntegrationBundle\Application\Common\AbstractMappedObjectService;
use Divante\MagentoIntegrationBundle\Domain\Event\IntegratedObjectEvent;
use Divante\MagentoIntegrationBundle\Domain\Event\PostMappingObjectEvent;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperEventTypes;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\Concrete;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;

/**
 * Class MappedCategoryService
 * @package Divante\MagentoIntegrationBundle\Domain\Category
 */
class MappedCategoryService extends AbstractMappedObjectService
{
    /**
     * @param string $ids
     * @param string $instanceUrl
     * @param string $storeViewId
     * @return array
     */
    public function getCategories(string $ids, string $instanceUrl, string $storeViewId)
    {
        $configurations = $this->configRepository->getByConfiguration(
            $instanceUrl,
            $storeViewId
        );
        if (!$configurations || empty($configurations)) {
            return [
                "success" => false,
                "message" => sprintf(
                    "Couldn't find configuration object with params instanceUrl: %s and storeViewId: %s",
                    $instanceUrl,
                    $storeViewId
                )
            ];
        }
        $configuration = reset($configurations);
        $categories = $this->integratedObjectRepository->getObjects(explode(",", $ids), $configuration);
        $missingData = $this->getMissingIds($categories, $ids);

        $mappedObjects = [];
        foreach ($categories as $object) {
            try {
                $this->permissionChecker->checkElementPermission($object, 'get');
                $mappedObjects[$object->getId()] = $this->getMappedObject($object, $configuration);
            } catch (\Exception $exception) {
                return ['success' => false, 'missing_objects' => $missingData];
            }
        }

        return ['data' => $mappedObjects, 'missing_objects' => $missingData, 'success' => true];

    }

    /**
     * @param Concrete                 $object
     * @param IntegrationConfiguration $configuration
     * @return \stdClass
     * @throws \Exception
     */
    protected function getMappedObject(Concrete $object, IntegrationConfiguration $configuration): \stdClass
    {
        $this->eventDispatcher->dispatch(
            new IntegratedObjectEvent($object, $configuration),
            MapperEventTypes::PRE_CATEGORY_MAP
        );

        $out = $this->mapper->getOutObject($object);

        $categoryRoot = $configuration->getCategoryRoot();
        $rootPath     = $categoryRoot->getPath() . $categoryRoot->getKey();
        $out->path    = str_replace($rootPath, '', $object->getPath());

        $this->mapper->loadSelectFieldData($out, $object);
        $mappedObject = $this->mapper->map($out, $configuration, IntegrationHelper::OBJECT_TYPE_CATEGORY);
        $this->configService->setCategoryHierarchyAttribute($mappedObject, $configuration);

        $this->eventDispatcher->dispatch(
            new PostMappingObjectEvent($object, $configuration, $mappedObject),
            MapperEventTypes::POST_CATEGORY_MAP
        );

        return $mappedObject;
    }
}
