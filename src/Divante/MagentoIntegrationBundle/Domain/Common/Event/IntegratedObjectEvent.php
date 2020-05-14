<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Event;

use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class IntegratedObjectEvent
 * @package Divante\MagentoIntegrationBundle\Domain\Event
 */
class IntegratedObjectEvent extends Event
{
    /** @var AbstractElement */
    protected $object;
    /** @var IntegrationConfiguration */
    protected $configuration;

    /**
     * AbstractObjectDeleteEvent constructor.
     * @param AbstractElement          $object
     * @param IntegrationConfiguration $configuration
     */
    public function __construct(AbstractElement $object, IntegrationConfiguration $configuration)
    {
        $this->setObject($object);
        $this->setConfiguration($configuration);
    }

    /**
     * @return IntegrationConfiguration
     */
    public function getConfiguration(): IntegrationConfiguration
    {
        return $this->configuration;
    }

    /**
     * @param IntegrationConfiguration $configuration
     */
    public function setConfiguration(IntegrationConfiguration $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return AbstractElement
     */
    public function getObject(): AbstractElement
    {
        return $this->object;
    }

    /**
     * @param AbstractElement $object
     */
    public function setObject(AbstractElement $object): void
    {
        $this->object = $object;
    }
}
