<?php


namespace Divante\MagentoIntegrationBundle\Application\Integration\Magento;

use Divante\MagentoIntegrationBundle\Domain\Provider\RestOutputProviderInterface;
use Divante\MagentoIntegrationBundle\Infrastructure\Rest\RequestClient;
use GuzzleHttp\Exception\GuzzleException;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MagentoStoreFetcher
 * @package Divante\MagentoIntegrationBundle\Application\Integration\Magento
 */
class MagentoStoreFetcher
{
    private $urlProvider;
    /** @var RequestClient */
    private $client;
    /** @var mixed */
    private $cachedReseponse = "";

    /**
     * MagentoStoreFetcher constructor.
     * @param RestOutputProviderInterface $urlProvider
     * @param RequestClient $client
     */
    public function __construct(RestOutputProviderInterface $urlProvider, RequestClient $client)
    {
        $this->urlProvider = $urlProvider;
        $this->client = $client;
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    public function getStores(IntegrationConfiguration $configuration)
    {
        if (!$this->cachedReseponse) {
            $this->client
                ->setUri($this->urlProvider->getStoreViewsEndpointUrl())
                ->setType('getStores')
                ->setConfiguration($configuration)
                ->setMethod(Request::METHOD_GET);
            try {
                $response              = $this->client->send();
                $this->cachedReseponse = $response->getBody();
            } catch (GuzzleException $exception) {
                return "";
            }
        }
        return json_decode((string)$this->cachedReseponse, true);
    }
}
