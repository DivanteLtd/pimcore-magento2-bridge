<?php

namespace Divante\MagentoIntegrationBundle\Action\Admin\Integrator;

use Divante\MagentoIntegrationBundle\Domain\Admin\Request\GetIntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Domain\Admin\SendProductsService;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Divante\MagentoIntegrationBundle\Responder\MappedObjectJsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SendProductsAction
 * @package Divante\MagentoIntegrationBundle\Action\Admin\Integrator
 * @Route("/integration-configuration/send/products", methods={"POST"})
 */
class SendProductsAction
{
    /** @var SendProductsService */
    private $domain;

    /** @var JsonResponder */
    private $responder;

    /**
     * GetProductAction constructor.
     * @param SendProductsService      $domain
     * @param JsonResponder $responder
     */
    public function __construct(SendProductsService $domain, JsonResponder $jsonResponder)
    {
        $this->domain    = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @return JsonResponse
     */
    public function __invoke(GetIntegrationConfiguration $query)
    {
        $this->domain->excecuteCommandForAll($query);
        return $this->responder->createResponse([]);
    }
}
