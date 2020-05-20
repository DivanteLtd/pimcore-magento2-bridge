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
 * Class OnDeleteEventSubscriber
 * @package Divante\MagentoIntegrationBundle\Infrastructure\DataObject\
 */
class OnDeleteEventSubscriber implements EventSubscriberInterface
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
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::PRE_DELETE => [
                ['sendDeleteNotification', 0]
            ]
        ];
    }

    /**
     * @param DataObjectEvent $objectEvent
     * @throws \Exception
     */
    public function sendDeleteNotification(DataObjectEvent $objectEvent)
    {
        /** @var Concrete $object */
        $object = $objectEvent->getObject();
        if (in_array($object->getClassId(), $this->repository->getAllProductClasses())) {
            $this->notificationSender->sendDeleteStatus(
                $object,
                $this->repository->getByProduct($object),
                ObjectTypeHelper::PRODUCT
            );
        }

        if (in_array($object->getClassId(), $this->repository->getAllCategoryClasses())) {
            $this->notificationSender->sendDeleteStatus(
                $object,
                $this->repository->getByCategory($object),
                ObjectTypeHelper::CATEGORY
            );
        }
    }
}
