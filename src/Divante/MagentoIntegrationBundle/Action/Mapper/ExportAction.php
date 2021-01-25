<?php

namespace Divante\MagentoIntegrationBundle\Action\Mapper;

use Divante\MagentoIntegrationBundle\Application\Mapper\MapperExporter;
use Divante\MagentoIntegrationBundle\Responder\JsonFileResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExportAction
 * @package Divante\MagentoIntegrationBundle\Action\Mapper
 * @Route("/mappings/export/{type}/{id}")
 */
class ExportAction
{
    /** @var MapperExporter */
    private $domain;

    /** @var JsonFileResponder */
    private $responder;

    /**
     * SendCategoriesAction constructor.
     * @param MapperExporter $domain
     * @param JsonFileResponder $jsonFileResponder
     */
    public function __construct(MapperExporter $domain, JsonFileResponder $jsonFileResponder)
    {
        $this->domain = $domain;
        $this->responder = $jsonFileResponder;
    }

    /**
     * @param Request $query
     * @return JsonResponse
     * @throws \Exception
     */
    public function __invoke(Request $query): Response
    {
        return $this->responder->createResponse(
            sprintf("export_%s_mapping", $query->get('type')),
            $this->domain->getExportMappingData($query->get("id"), $query->get('type'))
        );
    }
}
