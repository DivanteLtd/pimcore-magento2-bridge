<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Product;

use Divante\MagentoIntegrationBundle\Domain\Common\Exception\ElementNotFoundException;
use Divante\MagentoIntegrationBundle\Application\Common\StatusService;
use Divante\MagentoIntegrationBundle\Infrastructure\DataObject\DataObjectEventListener;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Domain\Helper\MagentoMessageHelper;
use Divante\MagentoIntegrationBundle\Domain\Helper\ObjectStatusHelper;
use Divante\MagentoIntegrationBundle\Application\IntegrationConfiguration\IntegrationConfigurationService;
use Divante\MagentoIntegrationBundle\Action\Rest\Product\Type\UpdateStatus;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Infrastructure\Security\ElementPermissionChecker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ProductStatusService
 * @package Divante\MagentoIntegrationBundle\Domain\Product
 */
class ProductStatusService extends StatusService
{
    /** @var IntegrationConfigurationService */
    private $configService;

    /**
     * ProductStatusService constructor.
     * @param ElementPermissionChecker        $permissionChecker
     * @param EventDispatcherInterface        $eventDispatcher
     * @param IntegrationConfigurationService $configService
     */
    public function __construct(
        ElementPermissionChecker $permissionChecker,
        EventDispatcherInterface $eventDispatcher,
        IntegrationConfigurationService $configService
    ) {
        parent::__construct($permissionChecker, $eventDispatcher);
        $this->configService = $configService;
    }

    /**
     * @param UpdateStatus $updateStatusCommand
     * @throws ElementNotFoundException
     * @throws \Divante\MagentoIntegrationBundle\Domain\Common\Exception\NotPermittedException
     */
    public function updateStatus(UpdateStatus $updateStatusCommand)
    {
        $object = $this->getObjectById($updateStatusCommand->id);
        $this->permissionChecker->checkElementPermission($object, 'get');
        $configurations = $this->configService->getConfigurations(
            $object,
            IntegrationHelper::RELATION_TYPE_PRODUCT,
            $updateStatusCommand->instaceUrl,
            $updateStatusCommand->storeViewId
        );
        if (!$configurations) {
            throw new ElementNotFoundException(
                sprintf(
                    '[ERROR] Missing configuration for object: %d, instanceUrl:%s, store view: %d.',
                    $updateStatusCommand->id,
                    $updateStatusCommand->instaceUrl,
                    $updateStatusCommand->storeViewId
                )
            );
        }
        /** @var IntegrationConfiguration $configuration */
        $configuration = reset($configurations);
        $this->removeUpdateListeners(DataObjectEventListener::class);
        if (strpos($updateStatusCommand->message, MagentoMessageHelper::MAGENTO_SUCCESS_ADDED)) {
            $updateStatusCommand->status = ObjectStatusHelper::SYNC_STATUS_SENT;
        }
        $this->setStatusProperty($object, $configuration->getKey(), $updateStatusCommand->status);
        $object->setOmitMandatoryCheck(true);
        $object->save();
        $this->eventDispatcher->dispatch($updateStatusCommand, 'product.status.update');
    }
}
