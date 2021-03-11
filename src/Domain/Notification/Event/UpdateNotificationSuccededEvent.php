<?php

namespace Divante\MagentoIntegrationBundle\Domain\Notification\Event;

/**
 * Class UpdateNotificationSuccededEvent
 * @package Divante\MagentoIntegrationBundle\Domain\Notification\Event
 */
class UpdateNotificationSuccededEvent extends AbstractNotificationEvent
{
    public const NAME = 'magento-notification.update.succeed';
}
