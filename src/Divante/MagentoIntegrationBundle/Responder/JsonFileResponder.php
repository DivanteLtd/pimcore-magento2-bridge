<?php


namespace Divante\MagentoIntegrationBundle\Responder;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonFileResponder
 * @package Divante\MagentoIntegrationBundle\Responder
 */
class JsonFileResponder
{
    /**
     * @param string $filename
     * @param array $data
     * @return Response
     */
    public function createResponse(string $filename, array $data): Response
    {
        $filename .= ".json";
        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename);
        $response->setContent(json_encode($data, JSON_PRETTY_PRINT));

        return $response;
    }
}
