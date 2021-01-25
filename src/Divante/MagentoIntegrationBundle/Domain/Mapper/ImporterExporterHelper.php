<?php


namespace Divante\MagentoIntegrationBundle\Domain\Mapper;

use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Divante\MagentoIntegrationBundle\Domain\Mapper\Exception\MappingImportException;
use Divante\MagentoIntegrationBundle\Domain\Mapper\Model\Mapping;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImporterExporterHelper
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper
 */
class ImporterExporterHelper
{
    /**
     * @param UploadedFile $file
     * @return Mapping
     * @throws MappingImportException
     */
    public function extractFileMapping(UploadedFile $file): Mapping
    {
        if ($file->getMimeType() !== "application/json") {
            throw new MappingImportException("Invalid file format!");
        }

        try {
            $fileData = json_decode(file_get_contents($file), 1);
        } catch (\Exception $exception) {
            throw new MappingImportException($exception->getMessage());
        }

        $type = $this->extractType($fileData);
        $data = $this->extractMappings($fileData, $type);

        $mapping = new Mapping();
        $mapping->setType($type);
        $mapping->setData($data);

        return $mapping;
    }

    /**
     * @param array $mappings
     * @param string $type
     * @return array
     */
    public function buildFileMapping(array $mappings, string $type): array
    {
        $formatted = [];
        $formatted["type"] = $type;
        $formatted["data"] = $this->formatData($mappings, $type);

        return $formatted;
    }

    /**
     * @param array $mappings
     * @param string $type
     * @return array
     */
    protected function formatData(array $mappings, string $type): array
    {
        $formatted = [];
        foreach ($mappings as $mapping) {
            $baseFields = [
                "from" => $mapping[0],
                "to" => $mapping[1],
                "strategy" => $mapping[2],
                "relation_fields" => $mapping[3],
                "thumbnail" => $mapping[4],
            ];
            if ($type === ObjectTypeHelper::PRODUCT) {
                $configFields = [
                    "searchable" => !is_bool($mapping[5]) ? false : $mapping[5],
                    "filterable" => !is_bool($mapping[6]) ? false : $mapping[6],
                    "comparable" => !is_bool($mapping[7]) ? false : $mapping[7],
                    "visible" => !is_bool($mapping[8]) ? false : $mapping[8],
                    "used_in_product" => !is_bool($mapping[9]) ? false : $mapping[9],
                ];
            }
            $formatted[] = array_merge($baseFields, $configFields ?? []);
        }

        return $formatted;
    }

    /**
     * @param array $data
     * @return string
     * @throws MappingImportException
     */
    protected function extractType(array $data): string
    {
        if (!array_key_exists("type", $data)) {
            throw new MappingImportException("File is corrupted, no information about type provided");
        }

        return $data['type'];
    }

    /**
     * @param array $data
     * @param string $type
     * @return array
     */
    protected function extractMappings(array $data, string $type): array
    {
        $extracted = [];
        foreach ($data["data"] as $datum) {
             $baseFields = [
                $datum["from"],
                $datum["to"],
                $datum["strategy"],
                $datum["relation_fields"],
                $datum["thumbnail"]
            ];
            if ($type === ObjectTypeHelper::PRODUCT) {
                $configFields = [
                    $datum["searchable"],
                    $datum["filterable"],
                    $datum["comparable"],
                    $datum["visible"],
                    $datum["used_in_product"]
                ];
            }
            $extracted[] = array_merge($baseFields, $configFields ?? []);
        }

        return $extracted;
    }
}
