<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Action\Rest\Product;

use Divante\MagentoIntegrationBundle\Domain\Product\MappedProductService;
use Divante\MagentoIntegrationBundle\Domain\Product\Request\GetProduct;
use Divante\MagentoIntegrationBundle\Responder\MappedObjectJsonResponder;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetProductAction
 * @package Divante\MagentoIntegrationBundle\Action\Rest\Product
 * @Route("/webservice/rest/product", methods={"GET"})
 */
class GetProductAction
{
    /** @var MappedProductService */
    private $domain;

    /** @var MappedObjectJsonResponder */
    private $responder;

    /**
     * GetProductAction constructor.
     * @param MappedProductService      $domain
     * @param MappedObjectJsonResponder $responder
     */
    public function __construct(MappedProductService $domain, MappedObjectJsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    /**
     * @param GetProduct $query
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(GetProduct $query)
    {
        return $this->responder->createResponse($this->domain->getProducts($query));
    }
}
