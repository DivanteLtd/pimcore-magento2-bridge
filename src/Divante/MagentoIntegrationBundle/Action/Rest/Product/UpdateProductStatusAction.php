<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Rest\Product;

use Divante\MagentoIntegrationBundle\Action\Common\Type\UpdateStatusRequest;
use Divante\MagentoIntegrationBundle\Application\DataObject\StatusUpdater;
use Divante\MagentoIntegrationBundle\Responder\MappedObjectJsonResponder;
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
     * @param StatusUpdater $domain
     * @param MappedObjectJsonResponder $jsonResponder
     */
    public function __construct(StatusUpdater $domain, MappedObjectJsonResponder $jsonResponder)
    {
        $this->domain    = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param UpdateStatusRequest $input
     * @return JsonResponse
     * @throws \Exception
     */
    public function __invoke(UpdateStatusRequest $input): JsonResponse
    {
        return $this->responder->createResponse(
            $this->domain->updateProductStatus(
                $input->id,
                $input->instaceUrl,
                $input->storeViewId,
                $input->status,
                $input->message
            )
        );
    }
}
