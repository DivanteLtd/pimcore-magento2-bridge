<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\DataObject;

use Divante\MagentoIntegrationBundle\Application\DataObject\ObjectPropertyUpdater;
use Divante\MagentoIntegrationBundle\Application\DataObject\StatusUpdater;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OnPreUpdateEventSubscriber
 * @package Divante\MagentoIntegrationBundle\Infrastructure\DataObject
 */
class OnPreUpdateEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var StatusUpdater
     */
    protected $propertyUpdater;

    /**
     * OnPreUpdateEventSubscriber constructor.
     * @param ObjectPropertyUpdater $propertyUpdater
     */
    public function __construct(ObjectPropertyUpdater $propertyUpdater)
    {
        $this->propertyUpdater = $propertyUpdater;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::PRE_UPDATE => [
                ['setNotificationAttribute', 0]
            ],
        ];
    }

    /**
     * @param DataObjectEvent $objectEvent
     */
    public function setNotificationAttribute(DataObjectEvent $objectEvent)
    {
        $preUpdateObject = $objectEvent->getObject();
        $databaseObject = Concrete::getById($preUpdateObject->getId(), 1);
        if (!$preUpdateObject instanceof Concrete) {
            return;
        }
        if (!$databaseObject instanceof $preUpdateObject) {
            return;
        }

        $this->propertyUpdater->setMagentoNotificationProperty($databaseObject, $preUpdateObject);
    }
}
