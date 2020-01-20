<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Mapper;

use Pimcore\Model\Webservice\Data\DataObject\Element;
use Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy\MapStrategyInterface;

/**
 * Class MapperContext
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper
 */
class MapperContext
{
    /** @var MapStrategyInterface[] */
    protected $strategies = array();

    /**
     * @param MapStrategyInterface $strategy
     */
    public function addStrategy(MapStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @param Element    $field
     * @param \stdClass  $obj
     * @param array      $arrayMapping
     * @param mixed      $language
     * @param string     $className
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $className): void
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->canProcess($field)) {
                $strategy->map($field, $obj, $arrayMapping, $language, $className);
                return;
            }
        }
    }
}
