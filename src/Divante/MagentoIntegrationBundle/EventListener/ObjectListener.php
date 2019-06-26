<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        09/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\EventListener;

use Divante\MagentoIntegrationBundle\Event\IntegratedObjectEventFactory;
use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Service\IntegrationConfigurationService;

use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Log\Simple;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ObjectListener
 * @package Divante\MagentoIntegrationBundle\EventListener
 */
class ObjectListener
{
    /** @var IntegrationConfigurationService */
    protected $integrationService;

    /** @var EventDispatcherInterface  */
    protected $eventDispatcher;

    /** @var IntegratedObjectEventFactory  */
    protected $eventObjectFactory;

    /**
     * ObjectListener constructor.
     * @param IntegrationConfigurationService $integrationService
     * @param EventDispatcherInterface        $eventDispatcher
     * @param IntegratedObjectEventFactory    $eventObjectFactory
     */
    public function __construct(
        IntegrationConfigurationService $integrationService,
        EventDispatcherInterface $eventDispatcher,
        IntegratedObjectEventFactory $eventObjectFactory
    ) {
        $this->integrationService   = $integrationService;
        $this->eventDispatcher      = $eventDispatcher;
        $this->eventObjectFactory   = $eventObjectFactory;
    }


    /**
     * @param DataObjectEvent $event
     * @throws \Exception
     */
    public function onPreUpdate(DataObjectEvent $event): void
    {
        /** @var AbstractObject $object */
        $object = $event->getObject();
        if (!$object instanceof DataObject\Concrete) {
            return;
        }
        $configurationToDelete = $this->getIntegrationsToDelete($object);
        foreach ($configurationToDelete as $configuration) {
            $eventObject = $this->eventObjectFactory->createEvent(
                $object,
                $configuration,
                IntegratedObjectEventFactory::DELETE_EVENT_TYPE
            );
            $this->eventDispatcher->dispatch(IntegrationHelper::INTEGRATED_OBJECT_PRE_DELETE_EVENT_NAME, $eventObject);
        }
        $configurationsToUpdate = $this->getIntegrationsToUpdate($object);
        foreach ($configurationsToUpdate as $configuration) {
            $eventObject = $this->eventObjectFactory->createEvent(
                $object,
                $configuration,
                IntegratedObjectEventFactory::UPDATE_EVENT_TYPE
            );
            $this->eventDispatcher->dispatch(IntegrationHelper::INTEGRATED_OBJECT_PRE_UPADTE_EVENT_NAME, $eventObject);
        }
    }

    /**
     * @param DataObjectEvent $event
     * @throws ValidationException
     */
    public function onPostUpdate(DataObjectEvent $event): void
    {

        /** @var AbstractObject $object */
        $object = $event->getObject();
        if (!$object instanceof DataObject\Concrete
            || $object instanceof DataObject\Folder
            || !$object->isPublished()) {
            return;
        }

            $configurationListing = $this->integrationService->getConfigurations($object);
            /** @var IntegrationConfiguration $configuration */
            foreach ($configurationListing as $configuration) {
                try {
                    $eventObject = $this->eventObjectFactory->createEvent(
                        $object,
                        $configuration,
                        IntegratedObjectEventFactory::UPDATE_EVENT_TYPE
                    );
                    $this->eventDispatcher->dispatch(IntegrationHelper::INTEGRATED_OBJECT_UPADTE_EVENT_NAME, $eventObject);
                } catch (\Exception $exception) {
                    if ($configuration->getConnectionType($object) == IntegrationHelper::IS_PRODUCT) {
                        $type = 'product';
                    } else {
                        $type = 'category';
                    }
                    Simple::log(sprintf('magento2-connector/%s-integration', $type), $exception->getMessage());
                    throw new ValidationException('Could not send this object into remote service');
                }
            }

    }

    /**
     * @param DataObjectEvent $event
     */
    public function onPostDelete(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof DataObject\Concrete) {
            return;
        }

        foreach ($this->integrationService->getConfigurations($object) as $configuration) {
            $eventObject = $this->eventObjectFactory->createEvent(
                $object,
                $configuration,
                IntegratedObjectEventFactory::DELETE_EVENT_TYPE
            );
            $this->eventDispatcher->dispatch(IntegrationHelper::INTEGRATED_OBJECT_DELETE_EVENT_NAME, $eventObject);
        }
    }
    /**
     * @param DataObject\Concrete $object
     * @return array
     */
    protected function getIntegrationsToUpdate(DataObject\Concrete $object): array
    {
        if ($object->isPublished()) {
            return $this->integrationService->getConfigurations($object);
        }
        return [];
    }

    /**
     * @param DataObject\Concrete $object
     * @return array
     */
    protected function getIntegrationsToDelete(DataObject\Concrete $object): array
    {
        $originObject = DataObject\Concrete::getById($object->getId(), true);
        /**
         * If product after an update is not supposed to be updated, but was before we must remove it.
         */
        if ($originObject && $object->isPublished()) {
            $originObjectIntegrations = $this->integrationService->getConfigurations($originObject);
            $objectIntegrations       = $this->integrationService->getConfigurations($object);
            $diff                     = array_udiff(
                $originObjectIntegrations,
                $objectIntegrations,
                function ($integration1, $integration2) {
                    return $integration1->getId() - $integration2->getId();
                }
            );
            return $diff;
        } elseif ($originObject && $originObject->isPublished()) {
            return $this->integrationService->getConfigurations($originObject);
        }
        return [];
    }
}
