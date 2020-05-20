<?php

namespace Divante\MagentoIntegrationBundle\Action\Admin;

use Divante\MagentoIntegrationBundle\Action\Admin\Type\SendCategoriesType;
use Divante\MagentoIntegrationBundle\Application\BulkAction\BulkActionCommandExecutor;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SendCategoriesAction
 * @package Divante\MagentoIntegrationBundle\Action\Admin
 * @Route("/integration-configuration/send/categories")
 */
class SendCategoriesAction
{
    /** @var BulkActionCommandExecutor  */
    private $domain;

    /** @var JsonResponder */
    private $responder;

    /**
     * SendCategoriesAction constructor.
     * @param BulkActionCommandExecutor $domain
     * @param JsonResponder $jsonResponder
     */
    public function __construct(BulkActionCommandExecutor $domain, JsonResponder $jsonResponder)
    {
        $this->domain = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param SendCategoriesType $query
     * @return JsonResponse
     */
    public function __invoke(SendCategoriesType $query): JsonResponse
    {
        $this->domain->executeCommandSendCategories("all", $query->id);
        return $this->responder->createResponse([]);
    }
}
