<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        19/09/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\EventListener;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\Event\Delete\AssetDeleteEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Delete\CategoryDeleteEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Delete\ProductDeleteEvent;
use Divante\MagentoIntegrationBundle\Model\Event\IntegratedObjectEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Update\AssetUpdateEvent;
use Divante\MagentoIntegrationBundle\Service\RestClientBuilder;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class DeleteObjectEventListener
 * @package Divante\MagentoIntegrationBundle\EventListener
 */
class DeleteObjectEventListener
{
    /** @var RestClientBuilder  */
    protected $builder;

    /**
     * DeleteObjectEventListener constructor.
     * @param RestClientBuilder $builder
     */
    public function __construct(RestClientBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param IntegratedObjectEvent $event
     * @throws \Exception
     */
    public function hideChildrenElements(IntegratedObjectEvent $event)
    {
        /** @var Concrete $object */
        $object = $event->getObject();
        /** @var Concrete $child */
        foreach ($object->getChildren([
            AbstractObject::OBJECT_TYPE_VARIANT,
            AbstractObject::OBJECT_TYPE_FOLDER,
            AbstractObject::OBJECT_TYPE_OBJECT
        ]) as $child) {
            if ($child->isPublished()) {
                $child->setPublished(false);
                $child->save();
            }
        }
    }

    /**
     * @param IntegratedObjectEvent $event
     */
    public function setObjectDeleteStatus(IntegratedObjectEvent $event)
    {
        /** @var Concrete $object */
        $object = $event->getObject();

        $object->setProperty(
            IntegrationHelper::SYNC_PROPERTY_NAME,
            'text',
            IntegrationHelper::SYNC_STATUS_DELETE
        );
    }

    /**
     * @param IntegratedObjectEvent $event
     */
    public function sendRemoveNotification(IntegratedObjectEvent $event)
    {
        /** @var Concrete $object */
        $object = $event->getObject();
        $client = $this->builder->getClient($event->getConfiguration());
        if ($event instanceof AssetDeleteEvent) {
            $client->deleteAsset($object);
        } elseif ($event instanceof ProductDeleteEvent) {
            $client->deleteProduct($object);
        } elseif ($event instanceof CategoryDeleteEvent) {
            $client->deleteCategory($object);
        }
    }
}
