<?php

namespace Divante\MagentoIntegrationBundle\Action\Rest\Asset;

use Divante\MagentoIntegrationBundle\Action\Common\Type\AssetRequest;
use Divante\MagentoIntegrationBundle\Application\Asset\MappedAssetService;
use Divante\MagentoIntegrationBundle\Responder\MappedObjectJsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCategoryAction
 * @package Divante\MagentoIntegrationBundle\Action\Rest\Category
 * @Route("/webservice/rest/asset", methods={"GET"})
 */
class GetAssetAction
{
    /**
     * @var MappedAssetService
     */
    private $domain;

    /**
     * @var MappedObjectJsonResponder
     */
    private $responder;

    /**
     * GetCategoryAction constructor.
     * @param MappedAssetService $domain
     * @param MappedObjectJsonResponder $responder
     */
    public function __construct(MappedAssetService $domain, MappedObjectJsonResponder $responder)
    {
        $this->domain    = $domain;
        $this->responder = $responder;
    }

    /**
     * @param AssetRequest $query
     * @return JsonResponse
     * @throws \Exception
     */
    public function __invoke(AssetRequest $query): JsonResponse
    {
        return $this->responder->createResponse(
            $this->domain->getAsset(
                $query->id,
                $query->thumbnail
            )
        );
    }
}
