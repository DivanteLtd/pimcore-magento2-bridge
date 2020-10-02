<?php

namespace Divante\MagentoIntegrationBundle\Application\DataObject;

use Divante\MagentoIntegrationBundle\Application\Common\IntegratedObjectRepositoryInterface;
use Divante\MagentoIntegrationBundle\Domain\DataObject\Property\PropertyStatusHelper;
use Divante\MagentoIntegrationBundle\Infrastructure\Category\IntegratedCategoryRepository;
use Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration\IntegrationConfigurationRepository;
use Divante\MagentoIntegrationBundle\Infrastructure\Product\IntegratedProductRepository;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class StatusUpdater
 * @package Divante\MagentoIntegrationBundle\Application\DataObject
 */
class StatusUpdater implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var IntegratedProductRepository
     */
    private $productRepository;
    /**
     * @var IntegrationConfigurationRepository
     */
    private $configRepository;
    /**
     * @var ObjectPropertyUpdater
     */
    private $propertyUpdater;
    /**
     * @var IntegratedCategoryRepository
     */
    private $categoryRepository;

    /**
     * StatusUpdater constructor.
     * @param IntegratedProductRepository $productRepository
     * @param IntegrationConfigurationRepository $configRepository
     * @param ObjectPropertyUpdater $propertyUpdater
     * @param IntegratedCategoryRepository $categoryRepository
     */
    public function __construct(
        IntegratedProductRepository $productRepository,
        IntegrationConfigurationRepository $configRepository,
        ObjectPropertyUpdater $propertyUpdater,
        IntegratedCategoryRepository $categoryRepository
    ) {
        $this->configRepository = $configRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->propertyUpdater = $propertyUpdater;
    }

    /**
     * @param string $id
     * @param string $instanceUrl
     * @param string $storeViewId
     * @param string $status
     * @param string|null $message
     * @return bool[]
     * @throws \Exception
     */
    public function updateProductStatus(
        string $id,
        string $instanceUrl,
        string $storeViewId,
        string $status,
        string $message = null
    ) {
        return $this->updateStatus($id, $instanceUrl, $storeViewId, $status, $this->productRepository, $message);
    }

    /**
     * @param string $id
     * @param string $instanceUrl
     * @param string $storeViewId
     * @param string $status
     * @param string|null $message
     * @return array|bool[]
     * @throws \Exception
     */
    public function updateCategoryStatus(
        string $id,
        string $instanceUrl,
        string $storeViewId,
        string $status,
        string $message = null
    ) {
        return $this->updateStatus($id, $instanceUrl, $storeViewId, $status, $this->categoryRepository, $message);
    }

    /**
     * @param string $id
     * @param string $instanceUrl
     * @param string $storeViewId
     * @param string $status
     * @param IntegratedObjectRepositoryInterface $repository
     * @param string|null $message
     * @return bool[]
     * @throws \Exception
     */
    protected function updateStatus(
        string $id,
        string $instanceUrl,
        string $storeViewId,
        string $status,
        IntegratedObjectRepositoryInterface $repository,
        string $message = null
    ) {
        $configurations = $this->configRepository->getByConfiguration(
            $instanceUrl,
            $storeViewId
        );
        $configuration = reset($configurations);
        if (!$configuration instanceof IntegrationConfiguration) {
            return ['success' => false, "message" => "Configuration not found"];
        }
        $objects = $repository->getObjects(explode(',', $id), $configuration);
        if (count($objects) == 0) {
            return ['success' => false, "message" => "Object(s) to update not found"];
        }
        $object = reset($objects);
        if ($status == PropertyStatusHelper::STATUS_ERROR && $this->logger instanceof LoggerInterface) {
            $this->logger->error(sprintf(
                    "Sync error for Product (%s) instance (%s). Message: %s",
                    $id,
                    $instanceUrl,
                    $message
                )
            );
        }
        $this->propertyUpdater->setProperty($object, $configuration, $status);

        return ['success' => true];
    }
}
