<?php

namespace Divante\MagentoIntegrationBundle\Domain\Notification\Event;

use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;

/**
 * Class AbstractNotificationEvent
 * @package Divante\MagentoIntegrationBundle\Domain\Notification\Event
 */
abstract class AbstractNotificationEvent
{
    /** @var AbstractElement */
    private $object;
    /** @var IntegrationConfiguration */
    private $configuration;
    /** @var mixed */
    private $data;

    /**
     * AbstractNotificationEvent constructor.
     * @param AbstractElement $object
     * @param IntegrationConfiguration $configuration
     * @param mixed $responseData
     */
    public function __construct(AbstractElement $object, IntegrationConfiguration $configuration, $responseData)
    {
        $this->object = $object;
        $this->configuration = $configuration;
        $this->data = $responseData;
    }

    /**
     * @return AbstractElement
     */
    public function getObject(): AbstractElement
    {
        return $this->object;
    }

    /**
     * @return IntegrationConfiguration
     */
    public function getConfiguration(): IntegrationConfiguration
    {
        return $this->configuration;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return AbstractNotificationEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
