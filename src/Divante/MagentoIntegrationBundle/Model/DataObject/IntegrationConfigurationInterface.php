<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        30/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;

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
    /** @return \Pimcore\Model\DataObject\AbstractObject*/
    public function getParent();
    /** @return \Pimcore\Model\DataObject\AbstractObject*/
    public function getProductRoot();
    /** @return \Pimcore\Model\DataObject\AbstractObject*/
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

    /** @return int */
    public function getMagentoStore();
}
