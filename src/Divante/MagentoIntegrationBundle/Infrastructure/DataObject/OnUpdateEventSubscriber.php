<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\DataObject;

use Divante\MagentoIntegrationBundle\Application\Notification\NotificationSender;
use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration\IntegrationConfigurationRepository;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OnUpdateEventSubscriber
 * @package Divante\MagentoIntegrationBundle\Infrastructure\DataObject
 */
class OnUpdateEventSubscriber implements EventSubscriberInterface
{
    /** @var IntegrationConfigurationRepository */
    private $repository;
    /** @var NotificationSender */
    private $notificationSender;

    /**
     * OnUpdateEventSubscriber constructor.
     * @param IntegrationConfigurationRepository $repository
     * @param NotificationSender                 $notificationSender
     */
    public function __construct(
        IntegrationConfigurationRepository $repository,
        NotificationSender $notificationSender
    ) {
        $this->repository = $repository;
        $this->notificationSender = $notificationSender;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::POST_ADD => [
                ['sendUpdateNotification', 0]
            ],
            DataObjectEvents::POST_UPDATE => [
                ['sendUpdateNotification', 0]
            ]
        ];
    }

    /**
     * @param DataObjectEvent $objectEvent
     * @throws \Exception
     */
    public function sendUpdateNotification(DataObjectEvent $objectEvent)
    {
        if ($objectEvent->hasArgument("saveVersionOnly")) {
            return;
        }
        /** @var Concrete $object */
        $object = $objectEvent->getObject();
        if (in_array($object->getClassId(), $this->repository->getAllProductClasses())) {
            $this->notificationSender->sentUpdateStatus(
                $object,
                $this->repository->getByProduct($object),
                ObjectTypeHelper::PRODUCT
            );
        }

        if (in_array($object->getClassId(), $this->repository->getAllCategoryClasses())) {
            $this->notificationSender->sentUpdateStatus(
                $object,
                $this->repository->getByCategory($object),
                ObjectTypeHelper::CATEGORY
            );
        }
    }
}
