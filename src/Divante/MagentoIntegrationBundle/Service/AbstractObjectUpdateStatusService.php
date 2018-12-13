<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        20/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service;

use Divante\MagentoIntegrationBundle\EventListener\ObjectListener;
use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Model\Request\AbstractObjectRequest;
use Divante\MagentoIntegrationBundle\Model\Request\UpdateStatus;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class AbstractObjectUpdateStatusService
 * @package Divante\MagentoIntegrationBundle\Service
 */
abstract class AbstractObjectUpdateStatusService extends AbstractObjectService
{
    const OBJECT_TYPE = null;

    /**
     * @param IntegrationConfiguration $configuration
     * @return mixed
     */
    abstract public function getObjectClass(IntegrationConfiguration $configuration);

    /**
     * @param AbstractObjectRequest $updateStatus
     * @param null                  $id
     * @return array
     */
    protected function getLoggedNotFoundResponse(AbstractObjectRequest $updateStatus, $id = null): array
    {
        if (!$updateStatus instanceof UpdateStatus) {
            return $this->getNotFoundResponse($updateStatus);
        }
        $this->getLogger()->error(
            sprintf(
                'Could not update status for object with id: %d. Status: %s. Message: %s',
                $updateStatus->id,
                $updateStatus->status,
                $updateStatus->message
            )
        );
        return $this->getNotFoundResponse($updateStatus);
    }

    /**
     * @param UpdateStatus $updateStatus
     * @return array
     */
    public function handleRequest(UpdateStatus $updateStatus): array
    {
        Concrete::setHideUnpublished(false);
        try {
            $object = Concrete::getById($updateStatus->id);
            if (!$object instanceof Concrete) {
                return $this->getLoggedNotFoundResponse($updateStatus);
            }
            $this->processObject($object, $updateStatus);
        } catch (\Exception $exception) {
            return $this->getLoggedNotFoundResponse($updateStatus);
        }
        return $this->getOkResponse();
    }

    /**
     * @param Concrete     $object
     * @param UpdateStatus $request
     * @throws \Exception
     */
    protected function processObject(Concrete $object, UpdateStatus $request)
    {
        $this->checkObjectPermission($object);
        $configuration = $this->getConfigurationForObject($object, $request);
        $isinObjectTree = ($configuration->getConnectionType($object) == static::OBJECT_TYPE);
        if (!($object->getClassId() == $this->getObjectClass($configuration)) || !$isinObjectTree) {
            throw new \Exception('No configuration was found for this object.');
        }
        $this->removeListeners();
        $object->setProperty(IntegrationHelper::SYNC_PROPERTY_NAME, 'text', $request->status);
        $object->setOmitMandatoryCheck(true);
        $this->logSyncStatus($object, $request);
        $object->save();
    }


    private function removeListeners(): void
    {
        $customerListener = $this->container->get(ObjectListener::class);
        $this->container->get('event_dispatcher')->removeListener(
            'pimcore.dataobject.preUpdate',
            [$customerListener, 'onPreUpdate']
        );
        $this->container->get('event_dispatcher')->removeListener(
            'pimcore.dataobject.postUpdate',
            [$customerListener, 'onPostUpdate']
        );
    }
}
