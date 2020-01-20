<?php

namespace Divante\MagentoIntegrationBundle\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JsonResponder
 */
class JsonResponder
{
    /**
     * @param array $data
     * @return JsonResponse
     */
    public function createResponse(array $data)
    {
        return new JsonResponse($data);
    }
}
