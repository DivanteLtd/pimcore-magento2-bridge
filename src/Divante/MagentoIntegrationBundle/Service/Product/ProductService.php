<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        20/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service\Product;

use Divante\MagentoIntegrationBundle\Event\Model\IntegratedMappedObjectEvent;
use Divante\MagentoIntegrationBundle\Event\Model\IntegratedObjectEvent;
use Divante\MagentoIntegrationBundle\Event\Type;
use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Interfaces\MapperInterface;
use Divante\MagentoIntegrationBundle\Model\Request\AbstractObjectRequest;
use Divante\MagentoIntegrationBundle\Model\Request\GetObject;
use Divante\MagentoIntegrationBundle\Model\Webservice\Data\DataObject\Concrete\Out;
use Divante\MagentoIntegrationBundle\Service\AbstractObjectService;
use Pimcore\Model\DataObject;

/**
 * Class ProductService
 * @package Divante\MagentoIntegrationBundle\Service\Product
 */
class ProductService extends AbstractObjectService
{

    /**
     * @param GetObject $request
     * @return array
     */
    public function handleRequest(GetObject $request): array
    {
        /** @var DataObject\Listing $objectsListing */
        $objectsListing = $this->loadObjects($request->id);
        $mappedObjects = [];
        /** @var array $fetchedIds */
        $missingData = $this->getMissingIds($objectsListing->loadIdList(), $request);

        /** @var DataObject\Concrete $object */
        foreach ($objectsListing->getObjects() as $object) {
            try {
                $mappedObject = $this->processObject($object, $request);
                $mappedObject->attr_checksum = $this->getMapper()->getAttributesChecksum($mappedObject);
                $mappedObjects[$object->getId()] = $mappedObject;
            } catch (\Exception $exception) {
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
     * @param DataObject\Concrete   $object
     * @param AbstractObjectRequest $request
     * @return array|\stdClass
     * @throws \Exception
     */
    protected function processObject(DataObject\Concrete $object, AbstractObjectRequest $request)
    {
        $this->checkObjectPermission($object);
        $configuration = $this->getConfigurationForObject($object, $request);

        /** @var DataObject\IntegrationConfiguration $configuration */
        if (!($configuration->getConnectionType($object) === IntegrationHelper::IS_PRODUCT)) {
            $this->getLoggedErrorMessage(
                sprintf('User has requested object: %d as a product, but it is not configured.', $object->getId())
            );
            throw new \Exception($this->getNotFoundMessage($object->getId()));
        }
        $this->eventDispatcher->dispatch(Type::PRE_PRODUCT_MAP, new IntegratedObjectEvent($object));

        /** @var Out $out */
        $out = $this->getOutObject($object);

        $mappedObject = $this->getMappedObject($object, $out, $configuration);

        $mappedObject->attr_checksum = $this->getMapper()->getAttributesChecksum($mappedObject);

        $this->eventDispatcher->dispatch(
            Type::POST_PRODUCT_MAP,
            new IntegratedMappedObjectEvent($mappedObject, $object)
        );

        return $mappedObject;
    }


    /**
     * @param DataObject\Concrete $element
     * @return array
     */
    protected function getSimpleProducts(DataObject\Concrete $element): array
    {
        DataObject\AbstractObject::setHideUnpublished(true);
        return array_map(
            function ($elem) {
                return $elem->getId();
            },
            $element->getChildren([
                DataObject\AbstractObject::OBJECT_TYPE_OBJECT
            ])
        );
    }

    /**
     * @param DataObject\Concrete $object
     * @param                     $out
     * @param                     $configuration
     * @return \stdClass
     */
    protected function getMappedObject(DataObject\Concrete $object, $out, $configuration): \stdClass
    {
        $this->getMapper()->loadSelectFieldData($out, $object);

        $mappedObject = $this->getMapper()->map($out, $configuration, IntegrationHelper::OBJECT_TYPE_PRODUCT);
        if (
            $object->hasProperty('configurable_attributes')
            && !(get_class($object->getParent()) == get_class($object))
        ) {
            $mappedObject->type = IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE;
            $this->mapper->enrichConfigurableProduct(
                $mappedObject,
                $object
            );
        } elseif (get_class($object->getParent()) == get_class($object)) {
            $mappedObject->type = DataObject\AbstractObject::OBJECT_TYPE_VARIANT;
            $this->mapper->enrichConfigurableProduct(
                $mappedObject,
                $object
            );
        }
        return $mappedObject;
    }
}
