<?php

namespace Divante\MagentoIntegrationBundle\Application\Notification;

use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;

/**
 * Interface NotificatorInterface
 * @package Divante\MagentoIntegrationBundle\Application\Notification
 */
interface NotificatorInterface
{
    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function sendUpdate(AbstractElement $element, IntegrationConfiguration $configuration): void;

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function sendDelete(AbstractElement $element, IntegrationConfiguration $configuration): void;

    /**
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool;
}
