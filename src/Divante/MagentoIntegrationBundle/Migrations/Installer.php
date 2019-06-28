<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        16/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Migrations;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Cache;
use Pimcore\Db\Connection;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Pimcore\Migrations\MigrationManager;
use Pimcore\Model\Property\Predefined;
use PimcoreDevkitBundle\Service\InstallerService;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Class Installer
 * @package Divante\MagentoIntegrationBundle\Migrations
 */
class Installer extends MigrationInstaller
{
    const CONFIGURATION_CLASS_NAME = 'integrationConfiguration';

    /** @var FileLocator  */
    protected $fileLocator;
    /**
     * @param BundleInterface  $bundle
     * @param Connection       $connection
     * @param MigrationManager $migrationManager
     * @param FileLocator      $fileLocator
     */
    public function __construct(
        BundleInterface $bundle,
        Connection $connection,
        MigrationManager $migrationManager,
        FileLocator $fileLocator
    ) {
        $this->fileLocator = $fileLocator;
        parent::__construct($bundle, $connection, $migrationManager);
    }

    /**
     * @param Schema  $schema
     * @param Version $version
     */
    public function migrateInstall(Schema $schema, Version $version): void
    {
        if (!Predefined::getByKey(IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE) instanceof Predefined) {
            $propertyData = [
                'name' => 'Configurable Attributes',
                'key' => IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE,
                'ctype' => 'object',
                'type' => 'text',
                'inheritable' => true
            ];
            $property = Predefined::create();
            $property->setValues($propertyData);
            $property->save();
        }

        Cache::disable();
        $service = new InstallerService();
        $service->createClassDefinition(
            self::CONFIGURATION_CLASS_NAME,
            $this->locateCustomViewFilePath()
        );

        Cache::enable();
        mkdir(PIMCORE_LOG_DIRECTORY . '/magento2-connector', 0740);
    }

    /**
     * @param Schema  $schema
     * @param Version $version
     */
    public function migrateUninstall(Schema $schema, Version $version): void
    {
        Cache::disable();
        $service = new InstallerService();
        $service->removeClassDefinition(self::CONFIGURATION_CLASS_NAME);
        Cache::enable();
    }

    /**
     * @return string
     */
    protected function locateCustomViewFilePath(): string
    {
        $filename =
            '@DivanteMagentoIntegrationBundle/Resources/install/classes/class_integrationConfiguration_export.json';
        return $this->fileLocator->locate($filename);
    }
}
