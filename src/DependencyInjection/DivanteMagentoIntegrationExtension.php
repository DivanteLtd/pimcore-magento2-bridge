<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        08/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\DependencyInjection;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\Custom\CustomStrategyInterface;
use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\MapStrategyInterface;
use Divante\MagentoIntegrationBundle\Application\Validator\Rules\ObjectValidationRuleInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class DivanteMagentoIntegrationExtension
 * @package Divante\MagentoIntegrationBundle\DependencyInjection
 */
class DivanteMagentoIntegrationExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(CustomStrategyInterface::class)
            ->addTag('object_mapper.customStrategy')
        ;
        $container->registerForAutoconfiguration(MapStrategyInterface::class)
            ->addTag('object_mapper.mapStrategy')
        ;
        $container->registerForAutoconfiguration(ObjectValidationRuleInterface::class)
            ->addTag('pimcore_connector.validator.common')
        ;
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
