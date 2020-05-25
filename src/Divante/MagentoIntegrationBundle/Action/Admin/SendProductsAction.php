<?php

namespace Divante\MagentoIntegrationBundle\Action\Admin;

use Divante\MagentoIntegrationBundle\Action\Admin\Type\SendProductsType;
use Divante\MagentoIntegrationBundle\Application\BulkAction\BulkActionCommandExecutor;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SendProductsAction
 * @package Divante\MagentoIntegrationBundle\Action\Admin
 * @Route("/integration-configuration/send/products")
 */
class SendProductsAction
{
    /** @var BulkActionCommandExecutor */
    private $domain;
    /** @var JsonResponder */
    private $responder;

    /**
     * SendProductsAction constructor.
     * @param BulkActionCommandExecutor $domain
     * @param JsonResponder             $jsonResponder
     */
    public function __construct(BulkActionCommandExecutor $domain, JsonResponder $jsonResponder)
    {
        $this->domain = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param SendProductsType $query
     * @return JsonResponse
     */
    public function __invoke(SendProductsType $query)
    {
        $this->domain->executeCommandSendProducts('all', $query->id);
        return $this->responder->createResponse([]);
    }
}
