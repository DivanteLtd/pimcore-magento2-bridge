<?php

namespace Divante\MagentoIntegrationBundle\Application\Mapper;

use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Divante\MagentoIntegrationBundle\Domain\Mapper\Exception\MappingExportException;
use Divante\MagentoIntegrationBundle\Domain\Mapper\ImporterExporterHelper;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Class MapperExporter
 * @package Divante\MagentoIntegrationBundle\Application\Mapper
 */
class MapperExporter
{
    /**
     * @var ImporterExporterHelper
     */
    protected $exporterHelper;

    /**
     * MapperExporter constructor.
     * @param ImporterExporterHelper $exporterHelper
     */
    public function __construct(ImporterExporterHelper $exporterHelper)
    {
        $this->exporterHelper = $exporterHelper;
    }

    /**
     * @param int $idConfig
     * @param string $type
     * @return array
     * @throws MappingExportException
     */
    public function getExportMappingData(int $idConfig, string $type): array
    {
        $config = IntegrationConfiguration::getById($idConfig);
        if (!$config instanceof IntegrationConfiguration) {
            throw new MappingExportException("Configuration not found!");
        }

        switch ($type) {
            case ObjectTypeHelper::PRODUCT:
                $mapping = $config->getProductMapping();
                break;
            case ObjectTypeHelper::CATEGORY:
                $mapping = $config->getCategoryMapping();
                break;
            default:
                throw new MappingExportException("Invalid type provided: " . $type);
        }

        return $this->exporterHelper->buildFileMapping($mapping, $type);
    }
}
