<?php

namespace Divante\MagentoIntegrationBundle\Domain\Notification\Event;

/**
 * Class DeleteNotificationFailedEvent
 * @package Divante\MagentoIntegrationBundle\Domain\Notification\Event
 */
class DeleteNotificationFailedEvent extends AbstractNotificationEvent
{
    public const NAME = 'magento-notification.delete.failed';
}

