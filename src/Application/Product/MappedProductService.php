<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Product;

use Divante\MagentoIntegrationBundle\Application\Common\AbstractMappedObjectService;
use Divante\MagentoIntegrationBundle\Domain\Common\Event\IntegratedObjectEvent;
use Divante\MagentoIntegrationBundle\Domain\Common\Event\PostMappingObjectEvent;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperEventTypes;
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
        $products = $this->integratedObjectRepository->getObjects(explode(",", $ids), $configuration);
        $missingData = $this->getMissingIds($products, $ids);
        $mappedObjects = [];
        foreach ($products as $object) {
            try {
                $this->permissionChecker->checkElementPermission($object, 'get');
                $mappedObjects[$object->getId()] = $this->getMappedObject($object, $configuration);
            } catch (\Exception $exception) {
                return ['success' => false];
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
        $mappedObject->attr_checksum = $this->attributeChecksum->getAttributesChecksum($mappedObject);

        $this->eventDispatcher->dispatch(
            new PostMappingObjectEvent($object, $configuration, $mappedObject),
            MapperEventTypes::POST_PRODUCT_MAP
        );

        return $mappedObject;
    }
}
