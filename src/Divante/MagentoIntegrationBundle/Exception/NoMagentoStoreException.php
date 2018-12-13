<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        30/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class NoMagentoStoreException
 * @package Divante\MagentoIntegrationBundle\Exception
 */
class NoMagentoStoreException extends \Exception implements ContainerExceptionInterface
{
}
