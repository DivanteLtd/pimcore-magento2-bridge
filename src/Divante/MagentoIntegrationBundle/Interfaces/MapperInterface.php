<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        29/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Interfaces;

use Pimcore\Model\DataObject\Concrete;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;

/**
 * Interface MapperInterface
 * @package Divante\MagentoIntegrationBundle\Interfaces
 */
interface MapperInterface
{
    /**
     * @param                          $out
     * @param IntegrationConfiguration $configuration
     * @param string                   $type
     * @return \stdClass
     */
    public function map($out, IntegrationConfiguration $configuration, string $type): \stdClass;

    /**
     * @param          $out
     * @param Concrete $object
     * @return mixed
     */
    public function loadSelectFieldData(&$out, Concrete $object);

    /**
     * @param \stdClass $object
     * @return array
     */
    public function getAttributesChecksum(\stdClass $object): array;

    /**
     * @param \stdClass $object
     * @param Concrete  $product
     */
    public function enrichConfigurableProduct(\stdClass &$object, Concrete $product): void;
}
