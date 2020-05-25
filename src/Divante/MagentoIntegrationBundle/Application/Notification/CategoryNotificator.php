<?php

namespace Divante\MagentoIntegrationBundle\Application\Notification;

use Divante\MagentoIntegrationBundle\Application\Integration\Magento\MagentoNotificationSender;
use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;

/**
 * Class CategoryNotificator
 * @package Divante\MagentoIntegrationBundle\Application\Notification
 */
class CategoryNotificator implements NotificatorInterface
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
     * @return void
     */
    public function sendUpdate(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->notificatorSender->sendCategoryUpdate($element, $configuration);
    }

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     * @return void
     */
    public function sendDelete(AbstractElement $element, IntegrationConfiguration $configuration): void
    {
        $this->notificatorSender->sendCategoryDelete($element, $configuration);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === ObjectTypeHelper::CATEGORY;
    }
}
