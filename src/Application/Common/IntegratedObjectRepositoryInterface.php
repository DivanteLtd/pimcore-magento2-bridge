<?php

namespace Divante\MagentoIntegrationBundle\Application\Common;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Interface IntegratedObjectRepositoryInterface
 * @package Divante\MagentoIntegrationBundle\Application\Common
 */
interface IntegratedObjectRepositoryInterface
{
    /**
     * @param IntegrationConfiguration $configuration
     * @return array
     */
    public function getAllObjects(IntegrationConfiguration $configuration): array;

    /**
     * @param array                    $objectsId
     * @param IntegrationConfiguration $configuration
     * @return Concrete[]
     */
    public function getObjects(array $objectsId, IntegrationConfiguration $configuration): array;
}
