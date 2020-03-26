<?php

namespace Divante\MagentoIntegrationBundle\Domain\Admin;

use Divante\MagentoIntegrationBundle\Domain\RemoteElementService;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Listing;

/**
 * Class AbstactSendService
 * @package Divante\MagentoIntegrationBundle\Domain\Admin
 */
abstract class AbstactSendService
{
    /** @var RemoteElementService */
    private $remoteElementService;

    /**
     * SendProductsService constructor.
     * @param RemoteElementService $remoteElementService
     */
    public function __construct(RemoteElementService $remoteElementService)
    {
        $this->remoteElementService = $remoteElementService;
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    abstract protected function getObjectsRoot(IntegrationConfiguration $configuration): string;

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    abstract protected function getObjectClass(IntegrationConfiguration $configuration): string;

    /**
     * @param string $idObject
     * @param string $idConfiguration
     * @return array
     */
    public function sendObjects(string $idObject, string $idConfiguration): array
    {
        $configurationObj = IntegrationConfiguration::getById($idConfiguration);
        $objects = [];
        if ($idObject === "all") {
            $objects = $this->getObjects($configurationObj);
        }
        if (is_numeric($idObject)) {
            $products = $this->getObject($idProduct);
        }
        foreach ($objects as $object) {
            $this->remoteElementService->sendUpdateStatus($object, $configurationObj);
        }

        return $objects;
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return array
     */
    private function getObjects(IntegrationConfiguration $configuration): array
    {
        $classDefinition = ClassDefinition::getById($this->getObjectClass($configuration));
        $listing = new Listing();
        $listing->setCondition(
            "o_className = :className AND o_path LIKE :path AND o_published = 1",
            [
                "className" => $classDefinition->getName(),
                "path" => sprintf("%s", $this->getObjectsRoot($configuration) . "%")
            ]
        );

        return $listing->getObjects();
    }

    /**
     * @param int $idObject
     * @return array
     */
    private function getObject(int $idObject): array
    {
        $listing = new Listing();
        $listing->setCondition(
            "o_id = :id AND o_published = 1",
            [
                "id" => $idObject,
            ]
        );

        return $listing->getObjects();
    }
}
