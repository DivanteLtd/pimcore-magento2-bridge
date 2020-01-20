<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Domain\Common;

use Divante\MagentoIntegrationBundle\Domain\Common\Reqest\GetElement;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationConfigurationService;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MapperService;
use Divante\MagentoIntegrationBundle\Security\ElementPermissionChecker;
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

    public function __construct(
        ElementPermissionChecker $permissionChecker,
        IntegrationConfigurationService $configService,
        MapperService $mapper,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->configService     = $configService;
        $this->mapper            = $mapper;
        $this->eventDispatcher   = $eventDispatcher;
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
            $listing->setCondition(sprintf("o_id IN (%s)", implode(', ', array_fill(0, count($ids), '?'))), $ids);
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
     * @param                       $idArray
     * @param GetElement            $request
     * @return array
     */
    protected function getMissingIds($idArray, GetElement $request): array
    {
        $missingData = [];

        foreach (explode(',', $request->id) as $id) {
            if (!in_array($id, $idArray)) {
                $missingData[$id] = sprintf('Requested object with id %d does not exist.', $id);
            }
        }
        return $missingData;
    }
}
