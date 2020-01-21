<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Rest\Product;

use Divante\MagentoIntegrationBundle\Domain\Product\ProductStatusService;
use Divante\MagentoIntegrationBundle\Domain\Product\Request\UpdateStatus;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateProductStatusAction
 * @package Divante\MagentoIntegrationBundle\Action\Rest\Product
 * @Route("/webservice/rest/product/update-status", methods={"POST"})
 */
class UpdateProductStatusAction
{
    private $domain;
    private $responder;

    /**
     * UpdateAssetStatusAction constructor.
     * @param ProductStatusService $domain
     * @param JsonResponder        $jsonResponder
     */
    public function __construct(ProductStatusService $domain, JsonResponder $jsonResponder)
    {
        $this->domain    = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param UpdateStatus $input
     * @return JsonResponse
     * @throws \Exception
     */
    public function __invoke(UpdateStatus $input): JsonResponse
    {
        $this->domain->updateStatus($input);
        return $this->responder->createResponse([]);
    }
}
