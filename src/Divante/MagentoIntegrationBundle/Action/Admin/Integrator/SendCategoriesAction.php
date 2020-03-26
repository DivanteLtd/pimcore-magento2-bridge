<?php

namespace Divante\MagentoIntegrationBundle\Action\Admin\Integrator;

use Divante\MagentoIntegrationBundle\Domain\Admin\CommandExcecutor;
use Divante\MagentoIntegrationBundle\Domain\Admin\Request\GetIntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Domain\Admin\SendCategoriesService;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\Routing\Annotation\Route;
use Divante\MagentoIntegrationBundle\Responder\MappedObjectJsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SendCategoriesAction
 * @package Divante\MagentoIntegrationBundle\Action\Admin\Integrator
 * @Route("/integration-configuration/send/categories", methods={"POST"})
 */
class SendCategoriesAction
{
    /** @var CommandExcecutor */
    private $domain;

    /** @var JsonResponder */
    private $responder;

    /**
     * SendCategoriesAction constructor.
     * @param CommandExcecutor $domain
     * @param JsonResponder $jsonResponder
     */
    public function __construct(CommandExcecutor $domain, JsonResponder $jsonResponder)
    {
        $this->domain    = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @return JsonResponse
     */
    public function __invoke(GetIntegrationConfiguration $query)
    {
        $this->domain->excecuteCommandSendCategories($query);
        return $this->responder->createResponse([]);
    }
}
