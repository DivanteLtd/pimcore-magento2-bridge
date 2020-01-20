<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Asset\EventListener;

use Divante\MagentoIntegrationBundle\Domain\Asset\IntegratedAssetService;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Class AssetListener
 * @package Divante\MagentoIntegrationBundle\EventListener
 */
class AssetListener
{
    /**
     * @var IntegratedAssetService
     */
    private $assetService;

    /**
     * ObjectListener constructor.
     * @param IntegratedAssetService  $assetService
     */
    public function __construct(
        IntegratedAssetService $assetService
    ) {
        $this->assetService       = $assetService;
    }

    /**
     * @param AssetEvent $event
     */
    public function onPostAssetUpdate(AssetEvent $event): void
    {
        /** @var Asset $object */
        $object = $event->getElement();
        if (!$object instanceof Asset) {
            return;
        }
        $endpointsToNotify = $this->assetService->getDependentEndpoints($object);
        /** @var IntegrationConfiguration $configuration */
        foreach ($endpointsToNotify as $configuration) {
            $this->assetService->send($object, $configuration);
            $this->assetService->setSendStatus($object, $configuration);
        }
    }
}
