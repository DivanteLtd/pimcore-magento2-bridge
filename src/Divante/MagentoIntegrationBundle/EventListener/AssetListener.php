<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        30/05/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\EventListener;

use Divante\MagentoIntegrationBundle\Event\IntegratedObjectEventFactory;
use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Service\IntegratedObjectService;
use Divante\MagentoIntegrationBundle\Service\IntegrationConfigurationService;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AssetListener
 * @package Divante\MagentoIntegrationBundle\EventListener
 */
class AssetListener
{
    /** @var IntegrationConfigurationService */
    protected $integrationService;

    /** @var IntegratedObjectService */
    protected $objectService;

    /** @var IntegratedObjectEventFactory  */
    protected $eventFactory;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /**
     * ObjectListener constructor.
     * @param IntegrationConfigurationService $integrationService
     * @param IntegratedObjectService         $objectService
     * @param IntegratedObjectEventFactory    $eventFactory
     * @param EventDispatcherInterface        $eventDispatcher
     */
    public function __construct(
        IntegrationConfigurationService $integrationService,
        IntegratedObjectService $objectService,
        IntegratedObjectEventFactory $eventFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->integrationService = $integrationService;
        $this->objectService      = $objectService;
        $this->eventFactory       = $eventFactory;
        $this->eventDispatcher    = $eventDispatcher;
    }

    /**
     * @param AssetEvent $event
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onPostAssetUpdate(AssetEvent $event): void
    {
        /** @var Asset $object */
        $object = $event->getElement();
        if (!$object instanceof Asset) {
            return;
        }
        $endpointsToNotify = $this->objectService->getDependentEndpoints($object);
        /** @var IntegrationConfiguration $configuration */
        foreach ($endpointsToNotify as $configuration) {
            $eventObject = $this->eventFactory->createEvent(
                $object,
                $configuration,
                IntegratedObjectEventFactory::UPDATE_EVENT_TYPE
            );
            $this->eventDispatcher->dispatch(IntegrationHelper::INTEGRATED_OBJECT_UPADTE_EVENT_NAME, $eventObject);
        }
    }
}
