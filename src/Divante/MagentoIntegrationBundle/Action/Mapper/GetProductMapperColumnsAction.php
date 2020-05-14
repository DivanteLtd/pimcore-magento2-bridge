<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Mapper;

use Divante\MagentoIntegrationBundle\Application\Mapper\MapperColumnsService;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/object-mapper/get-columns-product", methods={"GET"})
 */
class GetProductMapperColumnsAction
{
    /** @var MapperColumnsService */
    private $domain;
    /** @var JsonResponder */
    private $responder;

    /**
     * GetProductMapperColumnsAction constructor.
     * @param MapperColumnsService $domain
     * @param JsonResponder        $jsonResponder
     */
    public function __construct(MapperColumnsService $domain, JsonResponder $jsonResponder)
    {
        $this->domain    = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        return $this->responder->createResponse(
            $this->domain->getColumnsForClass($request->get('configurationId'), 'product')
        );
    }
}
