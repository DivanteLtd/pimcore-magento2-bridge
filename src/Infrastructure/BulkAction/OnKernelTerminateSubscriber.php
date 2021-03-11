<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\BulkAction;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class OnKernelTerminateSubscriber
 * @package Divante\MagentoIntegrationBundle\Infrastructure\BulkAction\
 */
class OnKernelTerminateSubscriber implements EventSubscriberInterface
{
    /** @var callable */
    private $callable;

    /**
     * @param callable $callable
     */
    public function setCallable(callable $callable)
    {
        $this->callable = $callable;
    }
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => [
                ['runCallback', 0]
            ]
        ];
    }

    /**
     * @param TerminateEvent $event
     */
    public function runCallback(TerminateEvent $event)
    {
        $callable = $this->callable;

        if (!$callable) {
            return;
        }
        $callable();
    }
}
