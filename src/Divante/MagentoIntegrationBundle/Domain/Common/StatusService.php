<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Common;

use Divante\MagentoIntegrationBundle\Domain\Common\Exception\ElementNotFoundException;
use Divante\MagentoIntegrationBundle\Domain\Helper\ObjectStatusHelper;
use Divante\MagentoIntegrationBundle\Security\ElementPermissionChecker;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\AbstractElement;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractStatusService
 * @package Divante\MagentoIntegrationBundle\Domain\Common
 */
class StatusService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /**
     * @var ElementPermissionChecker
     */
    protected $permissionChecker;

    public function __construct(ElementPermissionChecker $permissionChecker, EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher   = $eventDispatcher;
        $this->permissionChecker = $permissionChecker;
    }

    public function setStatusProperty(AbstractElement $object, string $integrationName, string $status)
    {
        $property = $object->getProperty(ObjectStatusHelper::SYNC_PROPERTY_NAME);
        if ($property) {
            $data = json_decode($property, true);
        }
        if (!$data) {
            $data = [];
        }
        $data["Integration " . $integrationName] = $status;
        $object->setProperty(ObjectStatusHelper::SYNC_PROPERTY_NAME, 'text', json_encode($data));
    }

    /**
     * @param string $className
     */
    protected function removeUpdateListeners(string $className): void
    {
        $customerListener = $this->container->get($className);
        $this->container->get('event_dispatcher')->removeListener(
            'pimcore.dataobject.preUpdate',
            [$customerListener, 'onPreUpdate']
        );
        $this->container->get('event_dispatcher')->removeListener(
            'pimcore.dataobject.postUpdate',
            [$customerListener, 'onPostUpdate']
        );
    }

    /**
     * @param int $id
     * @return Concrete
     * @throws ElementNotFoundException
     */
    protected function getObjectById(int $id): Concrete
    {
        Concrete::setHideUnpublished(false);
        $object = Concrete::getById($id);
        if (!$object instanceof Concrete) {
            throw new ElementNotFoundException(
                sprintf("Object with id: %d does not exist.", $id)
            );
        }
        return $object;
    }
}
