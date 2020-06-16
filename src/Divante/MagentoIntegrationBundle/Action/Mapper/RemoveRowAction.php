<?php

namespace Divante\MagentoIntegrationBundle\Action\Mapper;

use Divante\MagentoIntegrationBundle\Application\Mapper\MapperManager;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RemoveRowAction
 * @package Divante\MagentoIntegrationBundle\Action\Mapper
 * @Route("/mappings/remove-row/{type}")
 */
class RemoveRowAction
{
    /** @var MapperManager  */
    private $domain;

    /** @var JsonResponder */
    private $responder;

    /**
     * SendCategoriesAction constructor.
     * @param MapperManager $domain
     * @param JsonResponder $jsonResponder
     */
    public function __construct(MapperManager $domain, JsonResponder $jsonResponder)
    {
        $this->domain = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param Request $query
     * @return JsonResponse
     * @throws \Exception
     */
    public function __invoke(Request $query): JsonResponse
    {
        return $this->responder->createResponse(
            $this->domain->removeRow(
                $query->request->get("id"),
                $query->get('type'),
                $query->request->get("toColumn")
            )
        );
    }
}

