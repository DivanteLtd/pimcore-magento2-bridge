<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\Common;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventListenersManager
 * @package Divante\MagentoIntegrationBundle\Infrastructure\DataObject
 */
class EventListenersManager
{
    /** @var array */
    private $managedListeners;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * EventListenersManager constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->managedListeners = [
            'pimcore.dataobject.preUpdate'  => $eventDispatcher->getListeners('pimcore.dataobject.preUpdate'),
            'pimcore.dataobject.postUpdate' => $eventDispatcher->getListeners('pimcore.dataobject.postUpdate'),
            'pimcore.asset.preUpdate'       => $eventDispatcher->getListeners('pimcore.asset.preUpdate'),
            'pimcore.asset.postUpdate'      => $eventDispatcher->getListeners('pimcore.asset.postUpdate')
        ];
    }

    /**
     * @return void
     */
    public function disableEventListeners(): void
    {
        foreach ($this->managedListeners as $key => $listeners) {
            foreach ($listeners as $listener) {
                $this->eventDispatcher->removeListener($key, $listener);
            }
        }
    }

    /**
     * @return void
     *
     */
    public function restoreEventListeners(): void
    {
        foreach ($this->managedListeners as $key => $listeners) {
            foreach ($listeners as $listener) {
                $this->eventDispatcher->addListener($key, $listener);
            }
        }
    }
}
