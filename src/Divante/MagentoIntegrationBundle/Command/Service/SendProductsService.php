<?php

namespace Divante\MagentoIntegrationBundle\Command\Service;

use Pimcore\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Domain\RemoteElementService;
use Pimcore\Model\DataObject\Listing;

/**
 * Class SendProductsService
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
     * @param string $idProduct
     * @param string $idConfiguration
     */
    public function sendProducts(string $idProduct, string $idConfiguration)
    {
        $configurationObj = IntegrationConfiguration::getById($idConfiguration);
        $products = [];
        if ($idProduct === "--all") {
            $products = $this->getProductIds($configurationObj);
        }
        if (is_int($idProduct)) {
            $products = $this->getProduct($idProduct);
        }

        foreach ($products as $product) {
            $this->remoteElementService->sendUpdateStatus($product, $configurationObj);
        }
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return array
     */
    private function getProducts(IntegrationConfiguration $configuration): array
    {
        $productClass = $configuration->getProductClass();
        $listing = new Listing();
        $listing->setCondition(
            "oo_className = :className AND o_path LIKE :path AND o_published = 1",
            [
                "className" => $productClass->getClassName(),
                "path" => sprintf("%s%", $configuration->getProductRoot())
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
            "o_id = :id",
            [
                "id" => $idProduct,
            ]
        );

        return $listing->getObjects();
    }
}
