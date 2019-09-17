<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        21/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service\Category;

use Divante\MagentoIntegrationBundle\Event\Model\IntegratedMappedObjectEvent;
use Divante\MagentoIntegrationBundle\Event\Model\IntegratedObjectEvent;
use Divante\MagentoIntegrationBundle\Event\Type;
use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\Request\GetObject;
use Divante\MagentoIntegrationBundle\Service\MapperService;
use Divante\MagentoIntegrationBundle\Service\AbstractObjectService;
use Pimcore\Log\Simple;
use Pimcore\Model\DataObject\Concrete;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;

/**
 * Class CategoryService
 * @package Divante\MagentoIntegrationBundle\Service\Category
 */
class CategoryService extends AbstractObjectService
{
    /**
     * @param GetObject $request
     * @return array
     */
    public function handleRequest(GetObject $request): array
    {
        $objectsListing = $this->loadObjects($request->id);
        $mappedObjects  = [];
        /** @var array $fetchedIds */
        $missingData = $this->getMissingIds($objectsListing->loadIdList(), $request);
        /** @var Concrete $object */
        foreach ($objectsListing->getObjects() as $object) {
            try {
                $this->validateObject($object, $request);
                $configuration = $this->getConfigurationForObject($object, $request);
                $mappedObjects[$object->getId()] = $this->getMappedObject($object, $configuration);
            } catch (\Exception $exception) {
                Simple::log('magento2-connector/category-integration', $exception->getMessage());
                $missingData[$object->getId()] = $this->getLoggedErrorMessage($exception->getMessage());
            }
        }

        if (!$mappedObjects) {
            return $this->getLoggedNotFoundResponse($request);
        }

        $data = ['data' => $mappedObjects, 'missing_objects' => $missingData, 'success' => true];
        return $data;
    }

    /**
     * @param Concrete  $object
     * @param GetObject $request
     * @throws \Exception
     */
    protected function validateObject(Concrete $object, GetObject $request)
    {
        $this->checkObjectPermission($object);
        $configuration = $this->getConfigurationForObject($object, $request);
        if ($configuration->getConnectionType($object) !== IntegrationHelper::IS_CATEGORY) {
            $msg = sprintf(
                'Object with id: %d was requested as a category,'
                . 'but was not configured for instance %s and store view %d',
                $request->id,
                $request->instaceUrl,
                $request->storeViewId
            );
            throw new \Exception($msg);
        }
    }

    /**
     * @param Concrete                 $object
     * @param IntegrationConfiguration $configuration
     * @return \stdClass
     * @throws \Exception
     */
    protected function getMappedObject(Concrete $object, IntegrationConfiguration $configuration): \stdClass
    {
        $this->eventDispatcher->dispatch(Type::PRE_CATEGORY_MAP, new IntegratedObjectEvent($object));

        $out = $this->getOutObject($object);

        $categoryRoot = $configuration->getCategoryRoot();
        $rootPath     = $categoryRoot->getPath() . $categoryRoot->getKey();
        $out->path    = str_replace($rootPath, '', $object->getPath());

        /** @var MapperService $mapper */
        $mapper = $this->getMapper();
        $mapper->loadSelectFieldData($out, $object);
        $mappedObject = $mapper->map($out, $configuration, IntegrationHelper::OBJECT_TYPE_CATEGORY);
        $this->getIntegrationService()->setCategoryHierarchyAttribute($mappedObject, $configuration);

        $this->eventDispatcher->dispatch(
            Type::POST_CATEGORY_MAP,
            new IntegratedMappedObjectEvent($mappedObject, $object)
        );

        return $mappedObject;
    }
}
