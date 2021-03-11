<?php

namespace Divante\MagentoIntegrationBundle\Application\Notification;

use Divante\MagentoIntegrationBundle\Application\Integration\Magento\MagentoNotificationSender;
use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;

/**
 * Class ProductNotificator
 * @package Divante\MagentoIntegrationBundle\Application\Notification
 */
class ProductNotificator implements NotificatorInterface
{

    /** @var MagentoNotificationSender */
    private $notificatorSender;

    /**
     * CategoryNotificator constructor.
     * @param MagentoNotificationSender $magentoNotificationSender
     */
    public function __construct(MagentoNotificationSender $magentoNotificationSender)
    {
        $this->notificatorSender = $magentoNotificationSender;
    }

    /**
     * @inheritDoc
     */
    public function sendUpdate(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->notificatorSender->sendProductUpdate($element, $configuration);
    }

    /**
     * @inheritDoc
     */
    public function sendDelete(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->notificatorSender->sendProductDelete($element, $configuration);
    }

    /**
     * @inheritDoc
     */
    public function supports(string $type): bool
    {
        return $type === ObjectTypeHelper::PRODUCT;
    }
}
