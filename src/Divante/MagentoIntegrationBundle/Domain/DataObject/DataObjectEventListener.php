<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\DataObject;

use Divante\MagentoIntegrationBundle\Domain\ElementDeleteService;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationConfigurationService;
use Divante\MagentoIntegrationBundle\Domain\RemoteElementService;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder;

/**
 * Class DataObjectEventListener
 * @package Divante\MagentoIntegrationBundle\Domain\DataObject
 */
class DataObjectEventListener
{
    /** @var IntegrationConfigurationService */
    protected $integrationService;
    /** @var RemoteElementService */
    private $remoteElementService;
    /** @var ElementDeleteService */
    private $deleteService;

    /**
     * DataObjectEventListener constructor.
     * @param IntegrationConfigurationService $integrationService
     * @param ElementDeleteService            $deleteService
     * @param RemoteElementService            $updateService
     */
    public function __construct(
        IntegrationConfigurationService $integrationService,
        ElementDeleteService $deleteService,
        RemoteElementService $updateService
    ) {
        $this->integrationService   = $integrationService;
        $this->deleteService        = $deleteService;
        $this->remoteElementService = $updateService;
    }

    /**
     * @param DataObjectEvent $event
     * @throws \Exception
     */
    public function onPreUpdate(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof Concrete) {
            return;
        }
        $configurationToDelete = $this->getIntegrationsToDelete($object);
        foreach ($configurationToDelete as $configuration) {
            $this->remoteElementService->setDeletedStatus($object, $configuration);
            $this->deleteService->deleteObject($object, $configuration);
        }
        $configurationsToUpdate = $this->getIntegrationsToUpdate($object);
        foreach ($configurationsToUpdate as $configuration) {
            $this->remoteElementService->validateElement($object, $configuration);
        }
    }

    /**
     * @param Concrete $object
     * @return array
     */
    protected function getIntegrationsToDelete(Concrete $object): array
    {
        $originObject = Concrete::getById($object->getId(), true);
        if (!$originObject instanceof Concrete) {
            return [];
        }
        if ($originObject->isPublished() && $object->isPublished()) {
            return $this->integrationService->getConfigurationsListDifference($object, $object);
        } elseif ($originObject->isPublished() && !$object->isPublished()) {
            return $this->integrationService->getConfigurations($originObject);
        }
        return [];
    }

    /**
     * @param Concrete $object
     * @return array
     */
    protected function getIntegrationsToUpdate(Concrete $object): array
    {
        if ($object->isPublished()) {
            return $this->integrationService->getConfigurations($object);
        }
        return [];
    }

    /**
     * @param DataObjectEvent $event
     */
    public function onPostUpdate(DataObjectEvent $event): void
    {
        /** @var AbstractObject $object */
        $object = $event->getObject();
        if (!$object instanceof Concrete
            || $object instanceof Folder) {
            return;
        }
        $configurations = $this->integrationService->getConfigurations($object);
        /** @var IntegrationConfiguration $configuration */
        foreach ($configurations as $configuration) {
            $this->remoteElementService->sendUpdateStatus($object, $configuration);
        }
    }

    /**
     * @param DataObjectEvent $event
     */
    public function onPostDelete(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof Concrete) {
            return;
        }
        $configurations = $this->integrationService->getConfigurations($object);

        foreach ($configurations as $configuration) {
            $this->remoteElementService->sendDeleteStatus($object, $configuration);
        }
    }
}
