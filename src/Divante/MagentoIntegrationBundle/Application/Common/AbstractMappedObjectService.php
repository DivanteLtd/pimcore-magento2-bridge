<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Common;

use Divante\MagentoIntegrationBundle\Application\IntegrationConfiguration\IntegrationConfigurationService;
use Divante\MagentoIntegrationBundle\Application\Mapper\MapperService;
use Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration\IntegrationConfigurationRepository;
use Divante\MagentoIntegrationBundle\Infrastructure\Security\ElementPermissionChecker;
use Pimcore\Model\DataObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractMapperObjectService
 * @package Divante\MagentoIntegrationBundle\Domain\Common
 */
abstract class AbstractMappedObjectService
{

    /** @var ElementPermissionChecker */
    protected $permissionChecker;
    /** @var IntegrationConfigurationService */
    protected $configService;
    /** @var MapperService */
    protected $mapper;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var IntegratedObjectRepositoryInterface */
    protected $integratedObjectRepository;
    /** @var IntegrationConfigurationRepository */
    protected $configRepository;

    /**
     * AbstractMappedObjectService constructor.
     * @param ElementPermissionChecker $permissionChecker
     * @param IntegrationConfigurationService $configService
     * @param MapperService $mapper
     * @param EventDispatcherInterface $eventDispatcher
     * @param IntegrationConfigurationRepository $configRepository
     * @param IntegratedObjectRepositoryInterface $integratedObjectRepository
     */
    public function __construct(
        ElementPermissionChecker $permissionChecker,
        IntegrationConfigurationService $configService,
        MapperService $mapper,
        EventDispatcherInterface $eventDispatcher,
        IntegrationConfigurationRepository $configRepository,
        IntegratedObjectRepositoryInterface $integratedObjectRepository
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->configService     = $configService;
        $this->mapper            = $mapper;
        $this->eventDispatcher   = $eventDispatcher;
        $this->configRepository  = $configRepository;
        $this->integratedObjectRepository = $integratedObjectRepository;
    }

    /**
     * @param $id
     * @return DataObject\Listing
     */
    protected function loadObjects($id): DataObject\Listing
    {
        $listing = new DataObject\Listing();
        $ids     = $this->filterIds($id);
        if (!$ids) {
            $listing->setCondition("false");
        } else {
            $listing->setCondition(
                sprintf("o_id IN (%s)", implode(', ', array_fill(0, count($ids), '?'))),
                $ids
            );
            $listing->setObjectTypes(
                [DataObject\AbstractObject::OBJECT_TYPE_VARIANT, DataObject\AbstractObject::OBJECT_TYPE_OBJECT]
            );
        }
        $listing->load();
        return $listing;
    }

    /**
     * @param string $ids
     * @return array
     */
    protected function filterIds(string $ids): array
    {
        $ids = explode(',', $ids);
        $ids = array_filter($ids, function ($elem) {
            return is_numeric($elem);
        });
        return $ids;
    }

    /**
     * @param array $elements
     * @param string $ids
     * @return array
     */
    protected function getMissingIds(array $elements, string $ids): array
    {
        $idArray =  array_map(function ($element) {
            return $element->getId();
        }, $elements);

        $missingData = [];

        foreach (explode(',', $ids) as $id) {
            if (!in_array($id, $idArray)) {
                $missingData[$id] = sprintf('Requested object with id %d does not exist.', $id);
            }
        }
        return $missingData;
    }
}
