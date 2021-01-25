<?php


namespace Divante\MagentoIntegrationBundle\Application\Mapper;

use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Divante\MagentoIntegrationBundle\Domain\Mapper\Exception\MappingImportException;
use Divante\MagentoIntegrationBundle\Domain\Mapper\ImporterExporterHelper;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MapperImporter
 * @package Divante\MagentoIntegrationBundle\Application\Mapper
 */
class MapperImporter
{
    /**
     * @var ImporterExporterHelper
     */
    protected $importerHelper;

    /**
     * MapperImporter constructor.
     * @param ImporterExporterHelper $importerHelper
     */
    public function __construct(ImporterExporterHelper $importerHelper)
    {
        $this->importerHelper = $importerHelper;
    }

    /**
     * @param string $idConfig
     * @param string $type
     * @param UploadedFile $file
     * @return void
     * @throws MappingImportException
     */
    public function importMappingData(string $idConfig, string $type, UploadedFile $file): void
    {
        $config = IntegrationConfiguration::getById($idConfig);
        if (!$config instanceof IntegrationConfiguration) {
            throw new MappingImportException("Configuration not found!");
        }

        $mapping = $this->importerHelper->extractFileMapping($file);
        if ($type !== $mapping->getType()) {
            throw new MappingImportException(
                sprintf(
                    "Wrong file, type is set to '%s' and should be '%s'",
                    $mapping->getType(),
                    $type
                )
            );
        }

        switch ($mapping->getType()) {
            case ObjectTypeHelper::PRODUCT:
                $config->setProductMapping($mapping->getData());
                break;
            case ObjectTypeHelper::CATEGORY:
                $config->setCategoryMapping($mapping->getData());
                break;
            default:
                throw new MappingImportException("Invalid type provided: " . $mapping->getType());
        }

        try {
            $config->setOmitMandatoryCheck(true)->save();
        } catch (\Exception $exception) {
            throw new MappingImportException($exception->getMessage());
        }
    }
}
