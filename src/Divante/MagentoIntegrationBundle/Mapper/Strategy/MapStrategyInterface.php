<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy;

use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Interface MapStrategyInterface
 * @package Divante\MagentoIntegrationBundle\Service\MapperService\Strategy
 */
interface MapStrategyInterface
{
    /**
     * @param Element   $field
     * @param \stdClass $obj
     * @param array     $arrayMapping
     * @param           $language
     * @param mixed     $definition
     * @param           $className
     * @return mixed
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $className);

    /**
     * @param Element $field
     * @return bool
     */
    public function canProcess(Element $field): bool;
}
