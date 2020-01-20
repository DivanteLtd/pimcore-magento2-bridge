<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class MappedObjectJsonResponder
 * @package Divante\MagentoIntegrationBundle\Responder
 */
class MappedObjectJsonResponder
{
    /**
     * @param array $data
     * @return JsonResponse
     */
    public function createResponse(array $data)
    {
        return new JsonResponse($data, $data['success'] ? 200 : 404);
    }
}
