<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        30/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Service\Asset;

use Divante\MagentoIntegrationBundle\EventListener\AssetListener;
use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\Request\UpdateStatus;
use Divante\MagentoIntegrationBundle\Service\AbstractObjectService;
use Pimcore\Log\Simple;
use Pimcore\Model\Asset;

/**
 * Class AssetStatusService
 * @package Divante\MagentoIntegrationBundle\Service\Asset
 */
class AssetStatusService extends AbstractObjectService
{
    /**
     * @param Asset        $asset
     * @param UpdateStatus $request
     * @throws \Exception
     */
    public function updateStatus(Asset $asset, UpdateStatus $request)
    {
        $this->removeListeners();
        $asset->setProperty(IntegrationHelper::SYNC_PROPERTY_NAME, 'text', $request->status);
        $this->logSyncStatus($asset, $request);
        $asset->save();
    }

    /**
     * @return void
     */
    protected function removeListeners(): void
    {
        $integrationListeners = $this->container->get(AssetListener::class);
        $this->container->get('event_dispatcher')->removeListener(
            'pimcore.asset.postUpdate',
            array($integrationListeners, 'onPostAssetUpdate')
        );
    }
}
