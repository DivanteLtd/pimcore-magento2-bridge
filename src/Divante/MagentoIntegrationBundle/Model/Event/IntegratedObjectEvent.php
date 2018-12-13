<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        19/09/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Model\Event;

use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class IntegratedObjectEvent
 * @package Divante\MagentoIntegrationBundle\Model\Event
 */
class IntegratedObjectEvent extends Event
{
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


    /** @var AbstractElement */
    protected $object;

    /** @var IntegrationConfiguration */
    protected $configuration;

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
