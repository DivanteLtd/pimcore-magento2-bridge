<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\MapClassificationStoreValue;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MapperStrategyCompilerPass
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper
 */
class MapperStrategyCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $contextDefinition  = $container->findDefinition(MapperContext::class);
        $strategyServiceIds = array_keys(
            $container->findTaggedServiceIds('object_mapper.mapStrategy')
        );

        foreach ($strategyServiceIds as $strategyServiceId) {
            $contextDefinition->addMethodCall(
                'addStrategy',
                [new Reference($strategyServiceId)]
            );
        }

        $contextDefinition  = $container->findDefinition(MapClassificationStoreValue::class);
        $strategyServiceIds = array_keys(
            $container->findTaggedServiceIds('object_mapper_classificationstore.mapStrategy')
        );
        foreach ($strategyServiceIds as $strategyServiceId) {
            $contextDefinition->addMethodCall(
                'addStrategy',
                [new Reference($strategyServiceId)]
            );
        }
    }
}
