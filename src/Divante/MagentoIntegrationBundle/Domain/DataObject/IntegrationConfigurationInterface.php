<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        30/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\DataObject;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Interface IntegrationConfigurationInterface
 * @package Divante\MagentoIntegrationBundle\Model\DataObject
 */
interface IntegrationConfigurationInterface
{
    /**
     * @return array
     */
    public function getDecodedProductMapping(): array;

    /**
     * @return array
     */
    public function getDecodedCategoryMapping(): array;

    /**
     * @return mixed
     */
    public function getDefaultLanguage();

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return AbstractObject
     */
    public function getParent();

    /**
     * @return AbstractObject
     */
    public function getProductRoot();

    /**
     * @return AbstractObject
     */
    public function getCategoryRoot();

    /**
     * @return mixed
     */
    public function getProductClass();

    /**
     * @return mixed
     */
    public function getCategoryClass();

    /**
     * @param Concrete $object
     * @return bool
     */
    public function areParentsPublished(Concrete $object): bool;

    /**
     * @return int
     */
    public function getMagentoStore();

    /**
     * @param Element $element
     * @param array $mappings
     * @return bool
     */
    public function canElementBeMapped(Element $element, array $mappings): bool;
}
