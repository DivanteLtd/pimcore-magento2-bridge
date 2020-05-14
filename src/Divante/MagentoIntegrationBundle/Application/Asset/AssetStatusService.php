<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Asset;

use Divante\MagentoIntegrationBundle\Infrastructure\Asset\EventListener\AssetListener;
use Divante\MagentoIntegrationBundle\Domain\Asset\Request\UpdateStatus;
use Divante\MagentoIntegrationBundle\Domain\Common\Exception\ElementNotFoundException;
use Divante\MagentoIntegrationBundle\Application\Common\StatusService;
use Divante\MagentoIntegrationBundle\Domain\Helper\ObjectStatusHelper;
use Pimcore\Model\Asset;

/**
 * Class AssetStatusService
 * @package Divante\MagentoIntegrationBundle\Domain\Asset
 */
class AssetStatusService extends StatusService
{
    /**
     * @param UpdateStatus $statusCommand
     * @throws \Exception
     */
    public function updateStatus(UpdateStatus $statusCommand): void
    {
        $this->removeListeners();
        $asset = Asset::getById($statusCommand->id);
        $this->permissionChecker->checkElementPermission($asset, 'get');
        if (!$asset instanceof Asset) {
            throw new ElementNotFoundException(sprintf("Asset with id: %d does not exist", $statusCommand->id));
        }
        $asset->setProperty(ObjectStatusHelper::SYNC_PROPERTY_NAME, 'text', $statusCommand->status);
        $this->eventDispatcher->dispatch($statusCommand, 'asset.status.update');
        $asset->save();
    }

    /**
     * @return void
     */
    protected function removeListeners(): void
    {
        $integrationListeners = $this->container->get(AssetListener::class);
        $this->eventDispatcher->removeListener(
            'pimcore.asset.postUpdate',
            array($integrationListeners, 'onPostAssetUpdate')
        );
    }
}
