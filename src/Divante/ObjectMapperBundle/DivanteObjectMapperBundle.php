<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        26/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\ObjectMapperBundle;

use Divante\ObjectMapperBundle\Mapper\MapperStrategyCompilerPass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DivanteObjectMapperBundle
 * @package Divante\ObjectMapperBundle
 */
class DivanteObjectMapperBundle extends AbstractPimcoreBundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MapperStrategyCompilerPass());
        parent::build($container);
    }

    /**
     * @inheritdoc
     */
    public function getNiceName()
    {
        return "Divante Object Mapper";
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return '0.3.0';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return "Map Out object attributes";
    }
}
