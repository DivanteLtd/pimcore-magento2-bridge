<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\DataObject;

use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OnPreUpdateEventSubscriber
 * @package Divante\MagentoIntegrationBundle\Infrastructure\DataObject
 */
class OnPreUpdateEventSubscriber implements EventSubscriberInterface
{
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
        $databaseObject = AbstractObject::getById($preUpdateObject->getId(), 1);
        if ($preUpdateObject->getType() === AbstractObject::OBJECT_TYPE_FOLDER) {
            return;
        }
        if (!$databaseObject instanceof $preUpdateObject) {
            return;
        }

        if (!$databaseObject->isPublished() && !$preUpdateObject->isPublished()) {
            $preUpdateObject->setProperty("notify_magento", 'bool', false);
        } else {
            $preUpdateObject->setProperty("notify_magento", 'bool', true);
        }
    }
}
