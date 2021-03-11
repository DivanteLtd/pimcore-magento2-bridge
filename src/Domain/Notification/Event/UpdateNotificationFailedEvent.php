<?php

namespace Divante\MagentoIntegrationBundle\Domain\Notification\Event;

/**
 * Class UpdateNotificationFailedEvent
 * @package Divante\MagentoIntegrationBundle\Domain\Notification\Event
 */
class UpdateNotificationFailedEvent extends AbstractNotificationEvent
{
    public const NAME = 'magento-notification.update.failed';
}
