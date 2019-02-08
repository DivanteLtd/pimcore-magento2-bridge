<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        26/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Model\DataObject;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class IntegrationConfiguration
 * @package Divante\MagentoIntegrationBundle\Model\DataObject
 */
abstract class IntegrationConfiguration extends Concrete implements IntegrationConfigurationInterface
{
    /** @var array */
    protected $productMapping;
    /** @var array */
    protected $categoryMapping;
    /** @var mixed */
    protected $defaultLanguage;
    /** @var array */
    protected $mappingArrays = [];
    /** @var mixed */
    protected $instanceUrl;
    /** @var mixed */
    protected $clientSecret;

    protected $magentoStore;

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }
    /**
     * @return mixed
     */
    public function getInstanceUrl()
    {
        return $this->instanceUrl;
    }

    /**
     * @return array
     */
    public function getDecodedProductMapping(): array
    {
        if (!array_key_exists('product', $this->mappingArrays) || !$this->mappingArrays['product']) {
            $this->mappingArrays['product'] = [];
            foreach ($this->productMapping as $map) {
                if ($map[0] != "") {
                    $this->mappingArrays['product'][$map[0]][] = $map[1];
                }
            }
        }
        return $this->mappingArrays['product'];
    }

    /**
     * @return array
     */
    public function getDecodedCategoryMapping(): array
    {
        if (!array_key_exists('category', $this->mappingArrays) || !$this->mappingArrays['category']) {
            $this->mappingArrays['category'] = [];
            foreach ($this->categoryMapping as $map) {
                if ($map[0] != "") {
                    $this->mappingArrays['category'][$map[0]][] = $map[1];
                }
            }
        }
        return $this->mappingArrays['category'];
    }

    /**
     * @return mixed
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * @param $object
     * @return int
     */
    public function getConnectionType($object): int
    {
        if ($object instanceof Asset) {
            return IntegrationHelper::IS_ASSET;
        }
        if (!$object instanceof Concrete) {
            return -1;
        }
        if ($this->getProductRoot() &&
            strpos($object->getPath(), $this->getProductRoot()->getFullPath()) === 0
            && $object->getClassId() == $this->getProductClass()
        ) {
            return IntegrationHelper::IS_PRODUCT;
        }
        if ($this->getCategoryRoot() &&
            strpos($object->getPath(), $this->getCategoryRoot()->getFullPath()) === 0
            && $object->getClassId() == $this->getCategoryClass()
        ) {
            return IntegrationHelper::IS_CATEGORY;
        }
        return -1;
    }

    /**
     * Returns false only when object is category and at least one of its parents is not published.
     * @param Concrete $object
     * @return bool
     */
    public function areParentsPublished(Concrete $object): bool
    {
        if ($this->getConnectionType($object) != IntegrationHelper::IS_CATEGORY) {
            return true;
        }
        /** @var Concrete $parent */
        $parent = $object->getParent();
        while ($parent->getId() != $this->getCategoryRoot()->getId()) {
            if (!$parent->isPublished()) {
                return false;
            }
            $parent = $parent->getParent();
        }
        return true;
    }

    /**
     * @return int
     */
    public function getMagentoStore()
    {
        return $this->magentoStore;
    }
}
