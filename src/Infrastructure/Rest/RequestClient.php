<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Infrastructure\Rest;

use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RestClient
 * @package Divante\MagentoIntegrationBundle\Rest
 */
class RequestClient
{
    use LoggerAwareTrait;

    /** @var string */
    private $method;
    /** @var string */
    private $uri;
    /** @var array */
    private $headers;
    /** @var string */
    private $query;
    /** @var string */
    private $type;
    /** @var IntegrationConfiguration */
    private $configuration;
    /** @var Client */
    private $httpClient;
    /** @var EventDispatcher */
    private $eventDispatcher;
    /** @var RestEventFactory */
    private $eventFactory;
    /** @var Request */
    private $request;
    /** @var array  */
    private $guzzleContainer = [];

    /**
     * RequestClient constructor.
     * @param GuzzleClientFactory      $clientFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param RestEventFactory         $eventFactory
     */
    public function __construct(
        GuzzleClientFactory $clientFactory,
        EventDispatcherInterface $eventDispatcher,
        RestEventFactory $eventFactory
    ) {
        $this->httpClient = $clientFactory->getGuzzleClient($this->guzzleContainer);
        $this->eventDispatcher = $eventDispatcher;
        $this->eventFactory = $eventFactory;
    }

    /**
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    public function send(): ?ResponseInterface
    {
        $this->prepareRequest();

        $this->eventDispatcher->dispatch(
            $this->eventFactory->createEvent($this->request, 'before')
        );
        try {
            $response = $this->httpClient->send($this->request);
            $this->logRequest();
            $this->logResponse($response);
            $this->eventDispatcher->dispatch(
                $this->eventFactory->createEvent($response, 'after')
            );
            return $response;
        } catch (GuzzleException $exception) {
            $this->logger->critical($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @return void
     */
    private function logRequest(): void
    {
        $transation = reset($this->guzzleContainer);
        $this->logger->info(
            sprintf(
                "\n====REQUEST====\nURI: %s\nMethod: %s\nHeades: %s\nBody: %s",
                (string) $transation['request']->getUri(),
                (string) $transation['request']->getMethod(),
                (string) http_build_query($transation['request']->getHeaders()),
                (string) $transation['request']->getBody()
            )
        );
    }

    /**
     * @param ResponseInterface $response
     */
    private function logResponse(ResponseInterface $response): void
    {
        $this->logger->info(
            sprintf(
                "\n====RESPONSE====\n%s",
                $response->getBody()
            )
        );
    }

    /**
     * @return void
     */
    private function prepareRequest(): void
    {
        $this->setUri($this->configuration->getInstanceUrl() . $this->getUri());
        $this->setHeaders();
        $this->request = new Request(
            $this->getMethod(),
            $this->getUri(),
            $this->getHeaders(),
            $this->getQuery()
        );
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return RequestClient
     */
    public function setMethod(string $method): RequestClient
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     * @return RequestClient
     */
    public function setUri($uri): RequestClient
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return $this
     */
    public function setHeaders(): RequestClient
    {
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->configuration->getClientSecret(),
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json'
        ];
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     * @return RequestClient
     */
    public function setQuery($query): RequestClient
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @param string $type
     * @return RequestClient
     */
    public function setType(string $type): RequestClient
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return RequestClient
     */
    public function setConfiguration(IntegrationConfiguration $configuration): RequestClient
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @return IntegrationConfiguration
     */
    public function getConfiguration(): IntegrationConfiguration
    {
        return $this->configuration;
    }
}
