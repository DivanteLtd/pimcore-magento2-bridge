<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\Asset;

use Divante\MagentoIntegrationBundle\Application\Notification\AssetNotificator;
use Divante\MagentoIntegrationBundle\Domain\DataObject\Property\PropertyStatusHelper;
use Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration\IntegrationConfigurationRepository;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Model\Asset;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AssetRelationSubscriber
 */
class AssetSubscriber implements EventSubscriberInterface
{
    /**
     * @var IntegrationConfigurationRepository
     */
    private $configurationRepository;

    /**
     * @var AssetNotificator
     */
    private $assetNotificator;

    /**
     * RelationalAssetListener constructor.
     * @param IntegrationConfigurationRepository $configurationRepository
     * @param AssetNotificator $assetNotificator
     */
    public function __construct(
        IntegrationConfigurationRepository $configurationRepository,
        AssetNotificator $assetNotificator
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->assetNotificator = $assetNotificator;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            "pimcore.asset.preUpdate" => 'onPreUpdate',
            "pimcore.asset.preDelete" => 'onPreDelete'
        ];
    }

    /**
     * @param AssetEvent $event
     * @throws \Exception
     */
    public function onPreUpdate(AssetEvent $event)
    {
        $asset = $event->getAsset();
        if (!$this->shouldAssetBeUpdated($asset)) {
            return;
        }
        $configurations = $this->getConfigurations($asset);
        foreach ($configurations as $configuration) {
            $this->assetNotificator->sendUpdate($asset, $configuration);
        }
    }

    /**
     * @param AssetEvent $event
     * @throws \Exception
     */
    public function onPreDelete(AssetEvent $event)
    {
        $asset = $event->getAsset();
        if (!$this->shouldAssetBeUpdated($asset)) {
            return;
        }
        $configurations = $this->getConfigurations($asset);
        foreach ($configurations as $configuration) {
            $this->assetNotificator->sendDelete($asset, $configuration);
        }
    }

    /**
     * @param Asset $asset
     * @return array
     * @throws \Exception
     */
    private function getConfigurations(Asset $asset): array
    {
        $property = $asset->getProperty(PropertyStatusHelper::PROPERTY_NAME);
        $data = json_decode($property, true);
        if (!$data) {
            return [];
        }
        $magentoStoresIds = array_keys($data);

        return $this->configurationRepository->getByIntegrationIds($magentoStoresIds);
    }

    /**
     * @param Asset $asset
     * @return bool
     */
    private function shouldAssetBeUpdated(Asset $asset): bool
    {
        $originalAsset = Asset::getById($asset->getId(), true);

        return $originalAsset->hasProperty(PropertyStatusHelper::PROPERTY_NAME);
    }
}
