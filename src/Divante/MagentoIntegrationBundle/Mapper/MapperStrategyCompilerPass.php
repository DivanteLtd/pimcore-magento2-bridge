<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper;

use Divante\MagentoIntegrationBundle\Mapper\Strategy\MapClassificationStoreValue;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MapperStrategyCompilerPass
 * @package Divante\MagentoIntegrationBundle\Mapper
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

        $classificationstoreContextDefinition = $container->findDefinition(MapClassificationStoreValue::class);
        $strategyClassificationStoreServiceId = array_keys(
            $container->findTaggedServiceIds('object_mapper_classificationstore.mapStrategy')
        );
        foreach ($strategyClassificationStoreServiceId as $serviceId) {
            $classificationstoreContextDefinition->addMethodCall(
                'addStrategy',
                [new Reference($serviceId)]
            );
        }
    }
}
