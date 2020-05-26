<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\DataObject;

use Divante\MagentoIntegrationBundle\Application\DataObject\ObjectPropertyUpdater;
use Divante\MagentoIntegrationBundle\Domain\DataObject\Property\PropertyStatusHelper;
use Divante\MagentoIntegrationBundle\Domain\Notification\Event\AbstractNotificationEvent;
use Divante\MagentoIntegrationBundle\Domain\Notification\Event\UpdateNotificationFailedEvent;
use Divante\MagentoIntegrationBundle\Domain\Notification\Event\UpdateNotificationSuccededEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AfterObjectSentEventSubscriber
 * @package Divante\MagentoIntegrationBundle\Infrastructure\DataObject
 */
class AfterObjectSentEventSubscriber implements EventSubscriberInterface
{
    /** @var ObjectPropertyUpdater  */
    private $propertyUpdater;

    /** @var OnUpdateEventSubscriber */
    private $updateEventSubscriber;

    /**
     * AfterObjectSentEventSubscriber constructor.
     * @param ObjectPropertyUpdater   $propertyUpdater
     * @param OnUpdateEventSubscriber $eventSubscriber
     */
    public function __construct(ObjectPropertyUpdater $propertyUpdater, OnUpdateEventSubscriber $eventSubscriber)
    {
        $this->propertyUpdater = $propertyUpdater;
        $this->updateEventSubscriber = $eventSubscriber;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            UpdateNotificationSuccededEvent::class => [
                ['setStatusSendSuccess', 0]
            ],
            UpdateNotificationFailedEvent::class => [
                ['setStatusSendFailed', 0]
            ]
        ];
    }

    /**
     * @param AbstractNotificationEvent $event
     * @throws \Exception
     */
    public function setStatusSendSuccess(AbstractNotificationEvent $event)
    {
        $object = $event->getObject();
        $this->propertyUpdater->setProperty(
            $object,
            $event->getConfiguration(),
            PropertyStatusHelper::STATUS_SENT
        );
    }

    /**
     * @param AbstractNotificationEvent $event
     * @throws \Exception
     */
    public function setStatusSendFailed(AbstractNotificationEvent $event)
    {
        $object = $event->getObject();
        $this->propertyUpdater->setProperty(
            $object,
            $event->getConfiguration(),
            PropertyStatusHelper::STATUS_ERROR
        );
    }
}
