<?php

namespace Divante\MagentoIntegrationBundle\Application\BulkAction;

use Divante\MagentoIntegrationBundle\Application\Common\IntegratedObjectRepositoryInterface;
use Divante\MagentoIntegrationBundle\Application\Notification\NotificationSender;
use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class BulkUpdateService
 * @package Divante\MagentoIntegrationBundle\Application\BulkAction
 */
class BulkUpdateService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var NotificationSender */
    private $remoteElementService;
    /** @var IntegratedObjectRepositoryInterface */
    private $categoryRepository;
    /** @var IntegratedObjectRepositoryInterface */
    private $productRepository;

    /**
     * BulkUpdateService constructor.
     * @param NotificationSender $remoteElementService
     * @param IntegratedObjectRepositoryInterface $categoryRepository
     * @param IntegratedObjectRepositoryInterface $productRepository
     */
    public function __construct(
        NotificationSender $remoteElementService,
        IntegratedObjectRepositoryInterface $categoryRepository,
        IntegratedObjectRepositoryInterface $productRepository
    ) {
        $this->remoteElementService = $remoteElementService;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $objectIds
     * @param string $configurationId
     * @return Concrete[]
     */
    public function updateCategories(string $objectIds, string $configurationId): array
    {
        return $this->sendObjects($objectIds, $configurationId, ObjectTypeHelper::CATEGORY, $this->categoryRepository);
    }

    /**
     * @param string $objectIds
     * @param string $configurationId
     * @return Concrete[]
     */
    public function updateProducts(string $objectIds, string $configurationId): array
    {
        return $this->sendObjects($objectIds, $configurationId, ObjectTypeHelper::PRODUCT, $this->productRepository);
    }

    /**
     * @param string $idObject
     * @param string $idConfiguration
     * @param string $type
     * @param IntegratedObjectRepositoryInterface $repository
     * @return array
     */
    public function sendObjects(
        string $idObject,
        string $idConfiguration,
        string $type,
        IntegratedObjectRepositoryInterface $repository
    ): array {
        $configurationObj = IntegrationConfiguration::getById($idConfiguration);
        if (!$configurationObj instanceof IntegrationConfiguration) {
            throw new \InvalidArgumentException(sprintf('Integration with id %s does not exist', $idConfiguration));
        }
        switch (true) {
            case $idObject === "all":
                $objects = $repository->getAllObjects($configurationObj);
                break;
            case is_numeric($idObject):
                $objects = $repository->getObjects([$idObject], $configurationObj);
                break;
            default:
                $objects = $repository->getObjects(
                    array_filter(explode(',', $idObject), 'is_numeric'),
                    $configurationObj
                );
                break;
        }

        foreach ($objects as $object) {
            try {
                $this->remoteElementService->sentUpdateStatus($object, [$configurationObj], $type, true);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        return $objects;
    }
}
