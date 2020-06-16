<?php

namespace Divante\MagentoIntegrationBundle\Application\Mapper;

use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Divante\MagentoIntegrationBundle\Domain\Mapper\MagentoRequiredFields;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\ValidationException;
use Pimcore\Model\Version;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class MapperManager
 * @package Divante\MagentoIntegrationBundle\Application\Mapper
 */
class MapperManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param int $idConfig
     * @param string $type
     * @return array
     * @throws \Exception
     */
    public function addRow(int $idConfig, string $type): array
    {
        $config = IntegrationConfiguration::getById($idConfig);
        if (!$config instanceof IntegrationConfiguration) {
            return [
                "success" => false,
                "message" => "Cannot find configuration object"
            ];
        }

        try {
            switch ($type) {
                case ObjectTypeHelper::PRODUCT:
                    $newProductMappings = $this->getTableAfterAddition($config->getProductMapping());
                    $config->setProductMapping($newProductMappings);
                    break;
                case ObjectTypeHelper::CATEGORY:
                    $newCategoryMappings = $this->getTableAfterAddition($config->getCategoryMapping());
                    $config->setCategoryMapping($newCategoryMappings);
                    break;
                default:
                    throw new \Exception("Invalid type provided: " . $type);
            }
            Version::disable();
            $config->save();
            Version::enable();
        } catch (\Exception $exception) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->critical($exception->getMessage(), ["context" => $exception]);
            }
            return [
                "success" => false,
                "message" => "Fatal error during execution"
            ];
        }

        return [
            "success" => true
        ];
    }

    /**
     * @param int $idConfig
     * @param string $type
     * @param string|null $toColumn
     * @return array
     */
    public function removeRow(int $idConfig, string $type, ?string $toColumn): array
    {
        $config = IntegrationConfiguration::getById($idConfig);
        if (!$config instanceof IntegrationConfiguration) {
            return [
                "success" => false,
                "message" => "Cannot find configuration object"
            ];
        }

        try {
            switch ($type) {
                case ObjectTypeHelper::PRODUCT:
                    $newProductMappings = $this->getTableAfterRemoval($config->getProductMapping(), $toColumn);
                    $config->setProductMapping($newProductMappings);
                    break;
                case ObjectTypeHelper::CATEGORY:
                    $newCategoryMappings = $this->getTableAfterRemoval($config->getCategoryMapping(), $toColumn);
                    $config->setCategoryMapping($newCategoryMappings);
                    break;
                default:
                    throw new \Exception("Invalid type provided: " . $type);
            }
            Version::disable();
            $config->save();
            Version::enable();
        } catch (ValidationException $exception) {
            return [
                "success" => false,
                "message" => "Can't delete this row, field is required"
            ];
        } catch (\Exception $exception) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->critical($exception->getMessage(), ["context" => $exception]);
            }
            return [
                "success" => false,
                "message" => "Fatal error during execution"
            ];
        }

        return [
            "success" => true
        ];
    }

    /**
     * @param array $table
     * @param string|null $toColumn
     * @return array
     * @throws ValidationException
     */
    protected function getTableAfterRemoval(array $table, ?string $toColumn = null): array
    {
        if (in_array($toColumn, MagentoRequiredFields::REQUIRED_FIELDS)) {
            throw new ValidationException("Impossible to delete this field, it's required");
        }
        if (!$toColumn) {
            $row = end($table);
            if (in_array($row[1], MagentoRequiredFields::REQUIRED_FIELDS)) {
                throw new ValidationException("Impossible to delete this field, it's required");
            }
            array_pop($table);

            return $table;
        }

        foreach ($table as $index => $row) {
            if ($row[1] === $toColumn) {
                unset($table[$index]);
                break;
            }
        }

        return $table;
    }

    /**
     * @param array $table
     * @return array
     */
    protected function getTableAfterAddition(array $table): array
    {
        $newFields = array_filter($table, function ($row) {
            return strpos($row[1], "magento-field") !== false;
        });
        $table[] = ["", sprintf("magento-field-%s", count($newFields)), null, "", false, false, false, false, false];

        return $table;
    }
}
