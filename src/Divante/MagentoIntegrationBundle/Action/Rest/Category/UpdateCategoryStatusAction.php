<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Rest\Category;

use Divante\MagentoIntegrationBundle\Application\Category\CategoryStatusService;
use Divante\MagentoIntegrationBundle\Action\Rest\Category\Type\UpdateStatus;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
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
     * @param CategoryStatusService $domain
     * @param JsonResponder         $jsonResponder
     */
    public function __construct(CategoryStatusService $domain, JsonResponder $jsonResponder)
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
