<?php

namespace Divante\MagentoIntegrationBundle\Application\DataObject;

use Divante\MagentoIntegrationBundle\Infrastructure\DataObject\Relations\ObjectRelationRepository;
use Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration\IntegrationConfigurationRepository;
use Pimcore\Model\DataObject\ClassDefinition;

/**
 * Class RelationClassCollector
 * @package Divante\MagentoIntegrationBundle\Application\DataObject
 */
class RelationClassCollector
{
    /** @var IntegrationConfigurationRepository */
    private $configRepository;
    /** @var ObjectRelationRepository  */
    private $relationRepository;

    /**
     * RelationClassCollector constructor.
     * @param IntegrationConfigurationRepository $configRepository
     * @param ObjectRelationRepository $relationRepository
     */
    public function __construct(
        IntegrationConfigurationRepository $configRepository,
        ObjectRelationRepository $relationRepository
    ) {
        $this->configRepository = $configRepository;
        $this->relationRepository = $relationRepository;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRelatedClassFromConfigurations(): array
    {
        $classes = $this->relationRepository->getAllPossibleRelationClassesNames(
            $this->configRepository->getAllProductClasses(),
            $this->configRepository->getAllCategoryClasses()
        );
        $configurations = $this->configRepository->getAllConfigurations();

        $allowedClasses = [];
        foreach ($configurations as $configuration) {
            $allowed = $this->getRelatedClass($configuration->getProductClass());
            if (empty($allowed)) {
                $allowedClasses = $classes;
                break;
            }
            $allowedClasses = array_merge($allowedClasses, $allowed);

            $allowed = $this->getRelatedClass($configuration->getCategoryClass());
            if (empty($allowed)) {
                $allowedClasses = $classes;
                break;
            }
            $allowedClasses = array_merge($allowedClasses, $allowed);
        }

        return array_unique($allowedClasses);
    }

    /**
     * @param string $classId
     * @return array
     * @throws \Exception
     */
    public function getRelatedClass(string $classId): array
    {
        $integratedObject = ClassDefinition::getById($classId);
        $relationFields = array_filter($integratedObject->getFieldDefinitions(), function ($field) {
            return $field->isRelationType() && $field->getObjectsAllowed();
        });

        $allowedClasses = [];
        foreach ($relationFields as $relationField) {
            if (empty($relationField->getClasses())) {
                $allowedClasses = [];
                break;
            }
            $this->addFieldAllowedClasses($allowedClasses, $relationField);
        }

        return array_unique($allowedClasses);
    }

    /**
     * @param array $allowedClasses
     * @param $field
     */
    private function addFieldAllowedClasses(array &$allowedClasses, $field)
    {
        foreach ($field->getClasses() as $class) {
            $allowedClasses[] = $class['classes'];
        }
    }
}
