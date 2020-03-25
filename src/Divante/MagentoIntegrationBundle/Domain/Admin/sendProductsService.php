<?php

namespace Divante\MagentoIntegrationBundle\Domain\Admin;

use Divante\MagentoIntegrationBundle\Domain\Admin\Request\GetIntegrationConfiguration;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Domain\RemoteElementService;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\Process\Process;

/**
 * Class SendProducts
 */
class SendProductsService
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
     * @param string $idConfiguration
     */
    public function excecuteCommandForAll(GetIntegrationConfiguration $query)
    {
        $process = new Process("bin/console bin/console integration-magento:send:product all " . $query->id);
        $process->start();
    }
    /**
     * @param string $idProduct
     * @param string $idConfiguration
     */
    public function sendProducts(string $idProduct, string $idConfiguration): array
    {
        $configurationObj = IntegrationConfiguration::getById($idConfiguration);
        $products = [];
        if ($idProduct === "all") {
            $products = $this->getProducts($configurationObj);
        }
        if (is_numeric($idProduct)) {
            $products = $this->getProduct($idProduct);
        }
        foreach ($products as $product) {
            $this->remoteElementService->sendUpdateStatus($product, $configurationObj);
        }
        
        return $products;
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return array
     */
    private function getProducts(IntegrationConfiguration $configuration): array
    {
        $classDefinition = ClassDefinition::getById($configuration->getProductClass());
        $listing = new Listing();
        $listing->setCondition(
            "o_className = :className AND o_path LIKE :path AND o_published = 1",
            [
                "className" => $classDefinition->getName(),
                "path" => sprintf("%s", $configuration->getProductRoot() . "%")
            ]
        );

        return $listing->getObjects();
    }

    /**
     * @param int $idProduct
     * @return array
     */
    private function getProduct(int $idProduct): array
    {
        $listing = new Listing();
        $listing->setCondition(
            "o_id = :id AND o_published = 1",
            [
                "id" => $idProduct,
            ]
        );

        return $listing->getObjects();
    }
}
