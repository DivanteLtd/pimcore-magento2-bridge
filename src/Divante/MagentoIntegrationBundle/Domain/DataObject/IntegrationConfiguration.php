<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        26/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\DataObject;

use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration\AttributeType;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
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
                    $this->mappingArrays["product"][$map[0]][] = [
                        "field" => $map[1],
                        "strategy" => !empty($map[2]) ? $map[2] : null,
                        "attributes" => !empty($map[3]) ? $map[3] : null,
                        "attr_conf" => [
                            AttributeType::SEARCHABLE => $map[4],
                            AttributeType::FILTERABLE => $map[5],
                            AttributeType::COMPARABLE => $map[6],
                            AttributeType::VISIBLE_ON_FRONT => $map[7],
                            AttributeType::PRODUCT_LISTING => $map[8],
                        ]
                    ];
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
                    $this->mappingArrays["category"][$map[0]][] = [
                        "field" => $map[1],
                        "strategy" => !empty($map[2]) ? $map[2] : null,
                        "attributes" => !empty($map[3]) ? $map[3] : null
                    ];
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
     * Returns false only when object is category and at least one of its parents is not published.
     * @param Concrete $object
     * @return bool
     */
    public function areParentsPublished(Concrete $object): bool
    {
        if ($this->getRelationType($object) != IntegrationHelper::OBJECT_TYPE_CATEGORY) {
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
     * @param $object
     * @return int
     */
    public function getRelationType($object): int
    {
        if ($object instanceof Asset) {
            return IntegrationHelper::RELATION_TYPE_ASSET;
        }
        if (!$object instanceof Concrete) {
            return -1;
        }
        if ($this->getProductRoot() &&
            strpos($object->getPath(), $this->getProductRoot()->getFullPath()) === 0
            && $object->getClassId() == $this->getProductClass()
        ) {
            return IntegrationHelper::RELATION_TYPE_PRODUCT;
        }
        if ($this->getCategoryRoot() &&
            strpos($object->getPath(), $this->getCategoryRoot()->getFullPath()) === 0
            && $object->getClassId() == $this->getCategoryClass()
        ) {
            return IntegrationHelper::RELATION_TYPE_CATEGORY;
        }
        return -1;
    }

    /**
     * @return int
     */
    public function getMagentoStore()
    {
        return $this->magentoStore;
    }
}
