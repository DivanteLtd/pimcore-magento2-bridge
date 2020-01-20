<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain;

use Divante\MagentoIntegrationBundle\Domain\Common\IntegratedElementServiceInterface;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class ElementUpdateService
 * @package Divante\MagentoIntegrationBundle\Domain
 */
class RemoteElementService extends AbstractElementService
{
    /**
     * @param Concrete                 $object
     * @param IntegrationConfiguration $configuration
     */
    public function validateElement(Concrete $object, IntegrationConfiguration $configuration)
    {
        /** @var IntegratedElementServiceInterface $service */
        foreach ($this->remoteElementsServices as $service) {
            if ($service->supports($object, $configuration)) {
                $service->validate($object, $configuration);
            }
        }
    }

    /**
     * @param Concrete                 $object
     * @param IntegrationConfiguration $configuration
     */
    public function sendUpdateStatus(Concrete $object, IntegrationConfiguration $configuration)
    {
        foreach ($this->remoteElementsServices as $service) {
            if ($service->supports($object, $configuration)) {
                $service->send($object, $configuration);
                $service->setSendStatus($object, $configuration);
            }
        }
    }

    /**
     * @param Concrete                 $object
     * @param IntegrationConfiguration $configuration
     */
    public function sendDeleteStatus(Concrete $object, IntegrationConfiguration $configuration)
    {
        foreach ($this->remoteElementsServices as $service) {
            if ($service->supports($object, $configuration)) {
                $service->delete($object, $configuration);
                $service->setDeleteStatus($object, $configuration);
            }
        }
    }

    /**
     * @param Concrete                 $object
     * @param IntegrationConfiguration $configuration
     */
    public function setDeletedStatus(Concrete $object, IntegrationConfiguration $configuration)
    {
        foreach ($this->remoteElementsServices as $service) {
            if ($service->supports($object, $configuration)) {
                $service->delete($object, $configuration);
                $service->setDeleteStatus($object, $configuration);
            }
        }
    }
}
