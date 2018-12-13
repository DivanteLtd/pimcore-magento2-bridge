<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        12/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Event\Model;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class IntegratedObjectEvent
 * @package Divante\MagentoIntegrationBundle\Event\Model
 */
class IntegratedObjectEvent extends Event
{
    /** @var AbstractObject */
    protected $object;

    /**
     * IntegratedObjectEvent constructor.
     * @param AbstractObject $object
     */
    public function __construct(AbstractObject $object)
    {
        $this->setObject($object);
    }

    /**
     * @return AbstractObject
     */
    public function getObject(): AbstractObject
    {
        return $this->object;
    }

    /**
     * @param AbstractObject $object
     */
    public function setObject(AbstractObject $object): void
    {
        $this->object = $object;
    }
}
