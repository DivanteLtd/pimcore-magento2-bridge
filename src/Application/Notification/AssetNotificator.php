<?php

namespace Divante\MagentoIntegrationBundle\Application\Notification;

use Divante\MagentoIntegrationBundle\Application\Integration\Magento\MagentoNotificationSender;
use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;

/**
 * Class AssetNotificator
 * @package Divante\MagentoIntegrationBundle\Application\Notification
 */
class AssetNotificator implements NotificatorInterface
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
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function sendUpdate(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->notificatorSender->sendAssetUpdate($element, $configuration);
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function sendDelete(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->notificatorSender->sendAssetDelete($element, $configuration);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type == ObjectTypeHelper::ASSET;
    }

}
