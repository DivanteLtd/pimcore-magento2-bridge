<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        08/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle;

use Divante\MagentoIntegrationBundle\Mapper\MapperStrategyCompilerPass;
use Divante\MagentoIntegrationBundle\Migrations\Installer;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DivanteMagentoIntegrationBundle
 * @package Divante\MagentoIntegrationBundle
 */
class DivanteMagentoIntegrationBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;    

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
       parent::build($container);
       $container->addCompilerPass(new MapperStrategyCompilerPass());
    }

    /**
     * @inheritdoc
     */
    protected function getComposerPackageName()
    {
        return 'divante-ltd/pimcore-magento2-bridge';
    }

    /**
     * @inheritdoc
     */
    public function getNiceName()
    {
        return "Divante Pimcore 5 Magento 2 integration";
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return "Send information about products to Magento 2 eCommerce system";
    }

    /**
     * @return array
     */
    public function getJsPaths()
    {
        return [
            '/bundles/divantemagentointegration/js/pimcore/startup.js',
            '/bundles/divantemagentointegration/js/pimcore/item.js',
            '/bundles/divantemagentointegration/js/pimcore/productMapper.js',
            '/bundles/divantemagentointegration/js/pimcore/categoryMapper.js',
            '/bundles/divantemagentointegration/js/pimcore/uploadStatus.js',
            '/bundles/divantemagentointegration/js/pimcore/configurableAttributeSelectWindow.js',
            '/bundles/divantemagentointegration/js/pimcore/AnyPropertyGetter.js',
            '/bundles/divantemagentointegration/js/pimcore/properties.js',
        ];
    }

    /**
     * @return array
     */
    public function getCssPaths()
    {
        return [
            '/bundles/divantemagentointegration/css/mapper.css'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }
}
