<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        19/09/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Event;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Model\Event\IntegratedObjectEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Delete\AssetDeleteEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Delete\CategoryDeleteEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Delete\ProductDeleteEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Update\AssetUpdateEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Update\CategoryUpdateEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Update\ProductUpdateEvent;
use Pimcore\Model\Element\AbstractElement;

/**
 * Class IntegratedObjectEventFactory
 * @package Divante\MagentoIntegrationBundle\Event
 */
class IntegratedObjectEventFactory
{

    const DELETE_EVENT_TYPE = 1;
    const UPDATE_EVENT_TYPE = 2;

    /**
     * @param AbstractElement          $object
     * @param IntegrationConfiguration $configuration
     * @param mixed                    $type
     * @return IntegratedObjectEvent
     */
    public function createEvent(
        AbstractElement $object,
        IntegrationConfiguration $configuration,
        $type
    ): IntegratedObjectEvent {
        switch ($type) {
            case self::UPDATE_EVENT_TYPE:
                return $this->createUpdateEvent($object, $configuration);
                break;
            case self::DELETE_EVENT_TYPE:
                return $this->createDeleteEvent($object, $configuration);
                break;
            default:
                throw new \InvalidArgumentException('Unspported event type given');
        }
    }

    /**
     * @param AbstractElement          $object
     * @param IntegrationConfiguration $configuration
     * @return IntegratedObjectEvent
     */
    protected function createDeleteEvent(
        AbstractElement $object,
        IntegrationConfiguration $configuration
    ): IntegratedObjectEvent {
        switch ($configuration->getConnectionType($object)) {
            case IntegrationHelper::IS_PRODUCT:
                return new ProductDeleteEvent($object, $configuration);
                break;
            case IntegrationHelper::IS_CATEGORY:
                return new CategoryDeleteEvent($object, $configuration);
                break;
            case IntegrationHelper::IS_ASSET:
                return new AssetDeleteEvent($object, $configuration);
                break;
        }
        throw new \InvalidArgumentException('Object is not integrated');
    }

    /**
     * @param AbstractElement          $object
     * @param IntegrationConfiguration $configuration
     * @return IntegratedObjectEvent
     */
    protected function createUpdateEvent(
        AbstractElement $object,
        IntegrationConfiguration $configuration
    ): IntegratedObjectEvent {
        switch ($configuration->getConnectionType($object)) {
            case IntegrationHelper::IS_PRODUCT:
                return new ProductUpdateEvent($object, $configuration);
                break;
            case IntegrationHelper::IS_CATEGORY:
                return new CategoryUpdateEvent($object, $configuration);
                break;
            case IntegrationHelper::IS_ASSET:
                return new AssetUpdateEvent($object, $configuration);
                break;
        }
        throw new \InvalidArgumentException('Object is not integrated');
    }
}
