<?php

namespace Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\CalculatedValue;

/**
 * Class PathCalculator
 * @package Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration
 */
class PathCalculator
{
    /**
     * @param Concrete $object
     * @param CalculatedValue $context
     * @return mixed
     */
    public static function compute(Concrete $object, CalculatedValue $context)
    {
        if ($context->getFieldname() == 'productRootPath') {
            return $object->getProductRoot() ? $object->getProductRoot()->getFullPath() : '';
        } elseif ($context->getFieldname() == 'categoryRootPath') {
            return $object->getCategoryRoot() ? $object->getCategoryRoot()->getFullPath() : '';
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    "Field %s is not supported by this calculator",
                    $context->getFieldname()
                )
            );
        }
    }
}
