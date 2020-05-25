<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Rest\Asset;

use Divante\MagentoIntegrationBundle\Action\Common\Type\UpdateStatusRequest;
use Divante\MagentoIntegrationBundle\Application\Asset\StatusUpdater;
use Divante\MagentoIntegrationBundle\Domain\Common\Exception\NotPermittedException;
use Divante\MagentoIntegrationBundle\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateAssetStatusAction
 * @package Divante\MagentoIntegrationBundle\Action\Rest\Asset
 * @Route("/webservice/rest/asset/update-status", methods={"POST"})
 */
class UpdateAssetStatusAction
{
    /** @var UpdateStatusRequest */
    private $domain;
    /** @var JsonResponder */
    private $responder;

    /**
     * UpdateAssetStatusAction constructor.
     * @param UpdateStatusRequest $domain
     * @param JsonResponder $jsonResponder
     */
    public function __construct(StatusUpdater $domain, JsonResponder $jsonResponder)
    {
        $this->domain    = $domain;
        $this->responder = $jsonResponder;
    }

    /**
     * @param UpdateStatusRequest $updateStatus
     * @return JsonResponse
     * @throws NotPermittedException
     */
    public function __invoke(UpdateStatusRequest $updateStatus): JsonResponse
    {
        return $this->responder->createResponse(
            $this->domain->updateStatus(
                $updateStatus->id,
                $updateStatus->instaceUrl,
                $updateStatus->storeViewId,
                $updateStatus->status
            )
        );
    }
}
