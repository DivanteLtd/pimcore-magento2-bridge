<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\MapStrategyInterface;
use Divante\MagentoIntegrationBundle\Domain\Mapper\Model\IntegratedObject;
use Pimcore\Model\Webservice\Data\DataObject\Element;

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
     * @param Element $field
     * @param \stdClass $obj
     * @param array $arrayMapping
     * @param mixed $language
     * @param mixed $definition
     * @param $integratedObject
     */
    public function map(
        Element $field,
        \stdClass &$obj,
        array $arrayMapping,
        $language,
        $definition,
        IntegratedObject $integratedObject
    ): void {
        foreach ($this->strategies as $strategy) {
            if ($strategy->canProcess($field, $arrayMapping)) {
                $strategy->map($field, $obj, $arrayMapping, $language, $definition, $integratedObject);
            }
        }
    }
}
