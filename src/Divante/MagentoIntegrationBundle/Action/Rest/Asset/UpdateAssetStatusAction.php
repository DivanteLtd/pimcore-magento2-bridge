<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Action\Rest\Asset;

use Divante\MagentoIntegrationBundle\Domain\Asset\AssetStatusService;
use Divante\MagentoIntegrationBundle\Domain\Asset\Request\UpdateStatus;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateAssetStatusAction
 * @package Divante\MagentoIntegrationBundle\Action\Rest\Asset
 * @Route("/webservice/rest/asset/update-status", methods={"POST"})
 *
 */
class UpdateAssetStatusAction
{
    /** @var AssetStatusService  */
    private $domain;
    /** @var JsonResponder */
    private $responder;

    /**
     * UpdateAssetStatusAction constructor.
     * @param AssetStatusService $domain
     * @param JsonResponder      $jsonResponder
     */
    public function __construct(AssetStatusService $domain, JsonResponder $jsonResponder)
    {
        $this->domain = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param UpdateStatus $input
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function __invoke(UpdateStatus $input)
    {
        $this->domain->updateStatus($input);
        return $this->responder->createResponse([]);
    }
}
