<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\DataObject\Relations;

use Divante\MagentoIntegrationBundle\Application\BulkAction\BulkActionCommandExecutor;
use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration\IntegrationConfigurationRepository;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Webservice\Data\Mapper;
use Pimcore\Model\WebsiteSetting;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ObjectRelationSubscriber
 */
class ObjectRelationSubscriber implements EventSubscriberInterface
{
    /**
     * @var BulkActionCommandExecutor
     */
    private $bulkActionCommandExecutor;

    /**
     * @var ObjectRelationRepository
     */
    protected $objectRelationRepository;

    /**
     * @var IntegrationConfigurationRepository
     */
    private $configurationRepository;

    /**
     * RelationalAttributesListener constructor.
     * @param BulkActionCommandExecutor $bulkActionCommandExecutor
     * @param ObjectRelationRepository $objectRelationRepository
     * @param IntegrationConfigurationRepository $configurationRepository
     */
    public function __construct(
        BulkActionCommandExecutor $bulkActionCommandExecutor,
        ObjectRelationRepository $objectRelationRepository,
        IntegrationConfigurationRepository $configurationRepository
    ) {
        $this->bulkActionCommandExecutor = $bulkActionCommandExecutor;
        $this->objectRelationRepository = $objectRelationRepository;
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            "pimcore.dataobject.preUpdate" => 'onPreUpdate',
        ];
    }

    /**
     * @param DataObjectEvent $event
     * @throws \Exception
     */
    public function onPreUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if (!$this->shouldProductBeUpdated($object)) {
            return;
        }

        $productsClasses = $this->configurationRepository->getAllProductClasses();
        foreach ($productsClasses as $productsClass) {
            $results = $this->objectRelationRepository->getByRelationObject(
                $object,
                $productsClass,
                ObjectTypeHelper::PRODUCT
            );
            if (empty($results)) {
                continue;
            }
            $this->processResults($results, ObjectTypeHelper::PRODUCT);
        }

        $categoryClasses = $this->configurationRepository->getAllCategoryClasses();
        foreach ($categoryClasses as $categoryClass) {
            $results = $this->objectRelationRepository->getByRelationObject(
                $object,
                $categoryClass,
                ObjectTypeHelper::CATEGORY
            );
            if (empty($results)) {
                continue;
            }
            $this->processResults($results, ObjectTypeHelper::CATEGORY);
        }
    }

    /**
     * @param array $results
     * @param string $type
     */
    private function processResults(array $results, string $type): void
    {
        foreach ($results as $result) {
            if (!$result["objectIds"] || !$result["configuration"]) {
                continue;
            }
            if ($type === ObjectTypeHelper::PRODUCT) {
                $this->bulkActionCommandExecutor->executeCommandSendProducts(
                    $result["objectIds"],
                    $result["configuration"]
                );
            }
            if ($type === ObjectTypeHelper::CATEGORY) {
                $this->bulkActionCommandExecutor->executeCommandSendCategories(
                    $result["objectIds"],
                    $result["configuration"]
                );
            }
        }
    }

    /**
     * @param AbstractObject $object
     * @return bool
     * @throws \Exception
     */
    private function shouldProductBeUpdated(AbstractObject $object): bool
    {
        if ($object->getType() !== AbstractObject::OBJECT_TYPE_OBJECT) {
            return false;
        }
        $classname = $object->getClassname();
        $allowedClasses = WebsiteSetting::getByName(IntegrationHelper::WEBSITE_SETTINGS_ALLOWED_CLASSES);
        $allowedClasses = $allowedClasses instanceof WebsiteSetting
            ? json_decode($allowedClasses->getData(), true)
            : [];
        if (!in_array(ucfirst($classname), $allowedClasses)) {
            return false;
        }
        $objNamespace = sprintf("Pimcore\Model\DataObject\%s", $classname);
        $baseObject = $objNamespace::getById($object->getId(), true);

        $objectMap = Mapper::map(
            $object,
            '\\Pimcore\\Model\\Webservice\\Data\\DataObject\\Concrete\\Out',
            'out'
        );
        $baseObjectMap = Mapper::map(
            $baseObject,
            '\\Pimcore\\Model\\Webservice\\Data\\DataObject\\Concrete\\Out',
            'out'
        );


        if ($objectMap->elements == $baseObjectMap->elements && $objectMap->published == $baseObjectMap->published) {
            return false;
        }

        return true;
    }
}
