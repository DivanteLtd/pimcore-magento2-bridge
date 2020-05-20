<?php

namespace Divante\MagentoIntegrationBundle\Application\Notification;

use Divante\MagentoIntegrationBundle\Application\Validator\IntegratedObjectValidator;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Class NotificationSender
 * @package Divante\MagentoIntegrationBundle\Application\Notification
 */
class NotificationSender
{
    /** @var NotificatorInterface */
    protected $notificationServices;
    /** @var IntegratedObjectValidator */
    private $validator;

    /**
     * ObjectNotificationSender constructor.
     * @param iterable                  $remoteElementsServices
     * @param IntegratedObjectValidator $validator
     */
    public function __construct(
        iterable $remoteElementsServices,
        IntegratedObjectValidator $validator
    ) {
        $this->notificationServices = iterator_to_array($remoteElementsServices);
        $this->validator = $validator;
    }

    /**
     * @param Concrete $concrete
     * @param IntegrationConfiguration[] $configurations
     * @param string $type
     * @param bool $silent
     * @throws \Exception
     */
    public function sentUpdateStatus(
        Concrete $concrete,
        array $configurations,
        string $type,
        bool $silent = false
    ) {
        $sender = null;
        /** @var NotificatorInterface $service */
        foreach ($this->notificationServices as $service) {
            if ($service->supports($type)) {
                $sender = $service;
                break;
            }
        }
        if (!$service) {
            throw new \InvalidArgumentException(sprintf("Object type: %s is not supported", $type));
        }
        foreach ($configurations as $configuration) {
            if (!$this->validator->validateAbstractObject($concrete, $configuration, $type, $silent)) {
                continue;
            }
            $sender->sendUpdate($concrete, $configuration);
        }
    }

    /**
     * @param Concrete $concrete
     * @param IntegrationConfiguration[] $configurations
     * @param string $type
     * @throws \Exception
     */
    public function sendDeleteStatus(
        Concrete $concrete,
        array $configurations,
        string $type,
        bool $silent = false
    ) {
        if (!$configurations) {
            return;
        }
        $sender = null;
        /** @var NotificatorInterface $service */
        foreach ($this->notificationServices as $service) {
            if ($service->supports($type)) {
                $sender = $service;
                break;
            }
        }
        if (!$service) {
            throw new \InvalidArgumentException(sprintf("Object type: %s is not supported", $type));
        }
        foreach ($configurations as $configuration) {
            if (!$this->validator->validateAbstractObject($concrete, $configuration, $type, $silent)) {
                return;
            }
            $sender->sendDelete($concrete, $configuration);
        }
    }
}
