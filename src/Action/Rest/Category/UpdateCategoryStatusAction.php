<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Rest\Category;

use Divante\MagentoIntegrationBundle\Action\Common\Type\UpdateStatusRequest;
use Divante\MagentoIntegrationBundle\Application\DataObject\StatusUpdater;
use Divante\MagentoIntegrationBundle\Responder\MappedObjectJsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateCategoryStatusAction
 * @package Divante\MagentoIntegrationBundle\Action\Rest\Category
 * @Route("/webservice/rest/category/update-status", methods={"POST"})
 */
class UpdateCategoryStatusAction
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
            $this->domain->updateCategoryStatus(
                $input->id,
                $input->instaceUrl,
                $input->storeViewId,
                $input->status,
                $input->message
            )
        );
    }
}
