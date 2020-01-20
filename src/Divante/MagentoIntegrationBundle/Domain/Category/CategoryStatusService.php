<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Domain\Category;

use Divante\MagentoIntegrationBundle\Domain\Category\Request\UpdateStatus;
use Divante\MagentoIntegrationBundle\Domain\Common\StatusService;
use Divante\MagentoIntegrationBundle\Domain\Common\Exception\ElementNotFoundException;
use Divante\MagentoIntegrationBundle\Domain\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Domain\Helper\MagentoMessageHelper;
use Divante\MagentoIntegrationBundle\Domain\Helper\ObjectStatusHelper;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationConfigurationService;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Security\ElementPermissionChecker;
use Divante\MagentoIntegrationBundle\Domain\DataObject\DataObjectEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CategoryStatusService
 * @package Divante\MagentoIntegrationBundle\Domain\Category
 */
class CategoryStatusService extends StatusService
{
    /** @var IntegrationConfigurationService */
    private $configService;

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
     * @throws \Exception
     */
    public function updateStatus(UpdateStatus $updateStatusCommand)
    {
        $object = $this->getObjectById($updateStatusCommand->id);
        $this->permissionChecker->checkElementPermission($object, 'get');
        $configurations = $this->configService->getConfigurations(
            $object,
            IntegrationHelper::RELATION_TYPE_CATEGORY,
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
        $this->setStatusProperty($object,$configuration->getKey(), $updateStatusCommand->status);
        $object->setOmitMandatoryCheck(true);
        $object->save();
        $this->eventDispatcher->dispatch(
            $updateStatusCommand,
            'category.status.update'
        );
    }
}
