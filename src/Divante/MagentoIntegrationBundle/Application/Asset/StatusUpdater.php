<?php

namespace Divante\MagentoIntegrationBundle\Application\Asset;

use Divante\MagentoIntegrationBundle\Application\DataObject\ObjectPropertyUpdater;
use Divante\MagentoIntegrationBundle\Domain\Common\Exception\NotPermittedException;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration\AttributeType;
use Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration\IntegrationConfigurationRepository;
use Divante\MagentoIntegrationBundle\Infrastructure\Security\ElementPermissionChecker;
use Pimcore\Model\Asset;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class StatusUpdater
 * @package Divante\MagentoIntegrationBundle\Application\Asset
 */
class StatusUpdater implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var ElementPermissionChecker */
    protected $permissionChecker;
    /** @var IntegrationConfigurationRepository */
    protected $configRepository;
    /** @var ObjectPropertyUpdater */
    protected $propertyUpdater;

    /**
     * StatusUpdater constructor.
     * @param ElementPermissionChecker $permissionChecker
     * @param EventDispatcherInterface $eventDispatcher
     * @param ObjectPropertyUpdater $propertyUpdater
     * @param IntegrationConfigurationRepository $configRepository
     */
    public function __construct(
        ElementPermissionChecker $permissionChecker,
        EventDispatcherInterface $eventDispatcher,
        ObjectPropertyUpdater $propertyUpdater,
        IntegrationConfigurationRepository $configRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->permissionChecker = $permissionChecker;
        $this->propertyUpdater = $propertyUpdater;
        $this->configRepository = $configRepository;
    }

    /**
     * @param string $id
     * @param string $instanceUrl
     * @param string $storeViewId
     * @param string $status
     * @return array|bool[]
     * @throws NotPermittedException
     */
    public function updateStatus(string $id, string $instanceUrl, string $storeViewId, string $status): array
    {
        $configurations = $this->configRepository->getByConfiguration(
            $instanceUrl,
            $storeViewId
        );
        $configuration = reset($configurations);
        $assetId = explode(AttributeType::THUMBNAIL_CONCAT, $id)[0];
        $asset = Asset::getById($assetId);
        if (!$asset instanceof Asset) {
            return [
                'success' => false,
                'message' => sprintf("Asset with id: %d does not exist", $id)
            ];
        }
        $this->permissionChecker->checkElementPermission($asset, 'get');
        try {
            $this->propertyUpdater->setProperty($asset, $configuration, $status);
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }

        return ['success' => true];
    }
}
