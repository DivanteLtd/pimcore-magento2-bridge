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
use Pimcore\Model\DataObject;
use PimcoreDevkitBundle\Service\InstallerService;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Process\Process;

/**
 * Class Installer
 * @package Divante\MagentoIntegrationBundle\Migrations
 */
class Installer extends MigrationInstaller
{
    const CONFIGURATION_CLASS_NAME = 'IntegrationConfiguration';

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
        $classDefinition = $this->locateClassDefinitionFile();
        $command = ['bin/console', 'pimcore:definition:import:class', $classDefinition];
        $process = new Process($command, PIMCORE_PROJECT_ROOT);
        $process->setTimeout(0);
        $process->run();
        $this->outputWriter->write($process->getOutput());
        Cache::enable();
        if (!file_exists(PIMCORE_LOG_DIRECTORY . '/magento2-connector')) {
            mkdir(PIMCORE_LOG_DIRECTORY . '/magento2-connector', 0740);
        }
        $this->createSampleObject();
    }

    /**
     * @param Schema  $schema
     * @param Version $version
     */
    public function migrateUninstall(Schema $schema, Version $version): void
    {
        Cache::disable();
        $class = DataObject\ClassDefinition::getByName(static::CONFIGURATION_CLASS_NAME);
        if ($class) {
            $class->delete();
        }
        Cache::enable();
        rmdir(PIMCORE_LOG_DIRECTORY . '/magento2-connector');
    }

    /**
     * @return string
     */
    protected function locateClassDefinitionFile(): string
    {
        $filename =
            '@DivanteMagentoIntegrationBundle/Resources/install/classes/class_IntegrationConfiguration_export.json';
        return $this->fileLocator->locate($filename);
    }

    protected function createSampleObject()
    {
        $object = new DataObject\IntegrationConfiguration();
        $object->setParent(DataObject\Service::createFolderByPath('/integration-configuration'));
        $object->setPublished(false);
        $object->setOmitMandatoryCheck(true);
        $object->setKey('magento-configuration');
        $object->save();
    }
}
