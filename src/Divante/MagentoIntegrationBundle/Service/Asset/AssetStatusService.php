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
use Pimcore\Model\Asset;

/**
 * Class AssetStatusService
 * @package Divante\MagentoIntegrationBundle\Service\Asset
 */
class AssetStatusService extends AbstractObjectService
{
    /**
     * @param UpdateStatus $request
     * @return mixed
     */
    public function handleRequest(UpdateStatus $request)
    {
        try {
            $asset = $this->loadAsset($request->id);
            $this->checkObjectPermission($asset);
            $this->removeListeners();
            $asset->setProperty(IntegrationHelper::SYNC_PROPERTY_NAME, 'text', $request->status);
            $this->logSyncStatus($asset, $request);
            $asset->save();
        } catch (\Exception $exception) {
            return $this->getLoggedErrorMessage($exception->getMessage());
        }
        return $this->getOkResponse();
    }

    /**
     * @param int $id
     * @return Asset
     * @throws \Exception
     */
    protected function loadAsset(int $id): Asset
    {
        $asset = Asset::getById($id);
        if (!$asset instanceof Asset) {
            throw new \Exception(sprintf('Requested asset with id %d does not exists.', $id));
        }
        return $asset;
    }

    protected function removeListeners(): void
    {
        $integrationListeners = $this->container->get(AssetListener::class);
        $this->container->get('event_dispatcher')->removeListener(
            'pimcore.asset.postUpdate',
            array($integrationListeners, 'onPostAssetUpdate')
        );
    }
}
