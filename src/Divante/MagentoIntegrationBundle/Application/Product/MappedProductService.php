<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Product;

use Divante\MagentoIntegrationBundle\Application\Common\AbstractMappedObjectService;
use Divante\MagentoIntegrationBundle\Domain\Event\IntegratedObjectEvent;
use Divante\MagentoIntegrationBundle\Domain\Event\PostMappingObjectEvent;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperEventTypes;
use Divante\MagentoIntegrationBundle\Action\Rest\Product\Type\GetProduct;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class MappedProductService
 * @package Divante\MagentoIntegrationBundle\Domain\Product
 */
class MappedProductService extends AbstractMappedObjectService
{
    /**
     * @param string $ids
     * @param string $instanceUrl
     * @param string $storeViewId
     * @return array
     */
    public function getProducts(string $ids, string $instanceUrl, string $storeViewId): array
    {
        $objectsListing = $this->loadObjects($ids);
        $mappedObjects  = [];
        /** @var array $fetchedIds */
        $missingData = $this->getMissingIds($objectsListing->loadIdList(), $ids);

        /** @var Concrete $object */
        foreach ($objectsListing->getObjects() as $object) {
            try {
                $this->permissionChecker->checkElementPermission($object, 'get');
                $configurations = $this->configService->getConfigurations(
                    $object,
                    IntegrationHelper::RELATION_TYPE_PRODUCT,
                    $instanceUrl,
                    $storeViewId
                );
                if (!$configurations) {
                    $missingData[$object->getId()] = sprintf(
                        'Requested object with id %d does not exist.',
                        $object->getId()
                    );
                }

                $mappedObject                    = $this->getMappedObject($object, reset($configurations));
                $mappedObject->attr_checksum     = $this->mapper->getAttributesChecksum($mappedObject);
                $mappedObjects[$object->getId()] = $mappedObject;
            } catch (\Exception $exception) {
                return ['success' => false];
            }
        }

        if (!$mappedObjects) {
            return ['success' => false];
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
            MapperEventTypes::PRE_PRODUCT_MAP
        );

        $out = $this->mapper->getOutObject($object);
        $this->mapper->loadSelectFieldData($out, $object);
        $mappedObject = $this->mapper->map($out, $configuration, IntegrationHelper::OBJECT_TYPE_PRODUCT);
        if ($object->hasProperty(IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE)
            && !(get_class($object->getParent()) == get_class($object))
        ) {
            $mappedObject->type = IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE;
            $this->mapper->enrichConfigurableProduct(
                $mappedObject,
                $object
            );
        } elseif (get_class($object->getParent()) == get_class($object)) {
            $mappedObject->type = AbstractObject::OBJECT_TYPE_VARIANT;
            $this->mapper->enrichConfigurableProduct(
                $mappedObject,
                $object
            );
        }
        $mappedObject->attr_checksum = $this->mapper->getAttributesChecksum($mappedObject);

        $this->eventDispatcher->dispatch(
            new PostMappingObjectEvent($object, $configuration, $mappedObject),
            MapperEventTypes::POST_PRODUCT_MAP
        );

        return $mappedObject;
    }
}
