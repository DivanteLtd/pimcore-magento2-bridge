<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        29/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\ObjectMapperBundle\Mapper;

use Pimcore\Model\Webservice\Data\DataObject\Element;
use Divante\ObjectMapperBundle\Mapper\Strategy\MapStrategyInterface;

/**
 * Interface MapperContextInterface
 * @package Divante\ObjectMapperBundle\Mapper
 */
interface MapperContextInterface
{

    /**
     * @param MapStrategyInterface $strategy
     */
    public function addStrategy(MapStrategyInterface $strategy): void;

    /**
     * @param Element   $field
     * @param \stdClass $obj
     * @param array     $arrayMapping
     * @param           $language
     * @param           $className
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $className): void;
}
