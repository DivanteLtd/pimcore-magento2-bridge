<?php

namespace Divante\MagentoIntegrationBundle\Action\Mapper;

use Divante\MagentoIntegrationBundle\Application\Mapper\MapperImporter;
use Divante\MagentoIntegrationBundle\Domain\Mapper\Exception\MappingImportException;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ImportAction
 * @package Divante\MagentoIntegrationBundle\Action\Mapper
 * @Route("/mappings/import/{type}/{id}", methods={"POST"})
 */
class ImportAction
{
    /** @var MapperImporter  */
    private $domain;

    /** @var JsonResponder */
    private $responder;

    /**
     * SendCategoriesAction constructor.
     * @param MapperImporter $domain
     * @param JsonResponder $jsonResponder
     */
    public function __construct(MapperImporter $domain, JsonResponder $jsonResponder)
    {
        $this->domain = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param Request $query
     * @return JsonResponse
     */
    public function __invoke(Request $query): JsonResponse
    {
        try {
            $this->domain->importMappingData(
                $query->get('id'),
                $query->get('type'),
                $query->files->get('file')
            );
            return $this->responder->createResponse(
                [
                    "success" => true,
                ]
            );
        } catch (MappingImportException $exception) {
            return $this->responder->createResponse(
                [
                    "success" => false,
                    "message" => $exception->getMessage()
                ]
            );
        }
    }
}
