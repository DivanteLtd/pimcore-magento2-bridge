<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        01/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service;

use Divante\MagentoIntegrationBundle\Model\Configuration\EndpointConfig;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Provider\RestOutputProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Log\Simple;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\AbstractElement;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Throwable;

/**
 * Class RestClient
 * @package Divante\MagentoIntegrationBundle\Service
 */
class RestClient
{
    /** @var RestOutputProviderInterface */
    protected $provider;
    /** @var Client */
    protected $client;
    /** @var IntegrationConfiguration */
    protected $configuration;
    /** @var array */
    protected $defaultHeaders;
    /** @var ApplicationLogger */
    protected $logger;

    /**
     * RestClient constructor.
     * @param Client                      $client
     * @param RestOutputProviderInterface $provider
     * @param ApplicationLogger           $logger
     */
    public function __construct(Client $client, RestOutputProviderInterface $provider, ApplicationLogger $logger)
    {
        $this->client   = $client;
        $this->provider = $provider;
        $this->logger   = $logger;
    }

    public function setConfiguration(IntegrationConfiguration $configuration): void
    {
        $this->configuration = $configuration;
        $headers                      = [
            'Authorization' => 'Bearer ' . $configuration->getClientSecret(),
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json'
        ];
        $this->setDefaultHeaders($headers);

    }

    /**
     * @param array $headers
     */
    public function setDefaultHeaders(array $headers): void
    {
        $this->defaultHeaders = $headers;
    }

    /**
     * @return mixed
     */
    public function getDefaultHeaders()
    {
        return $this->defaultHeaders;
    }

    /**
     * @param Concrete $object
     */
    public function sendProduct(Concrete $object)
    {
        $this->sendObject($object, $this->provider->getProductConfig());
    }

    /**
     * @param Concrete $object
     * @return mixed
     */
    public function sendCategory(Concrete $object)
    {
        $this->sendObject($object, $this->provider->getCategoryConfig());
    }

    /**
     * @param Concrete $object
     */
    public function deleteProduct(Concrete $object)
    {
        $this->deleteObject($object, $this->provider->getProductConfig());
    }

    /**
     * @param Concrete $object
     */
    public function deleteCategory(Concrete $object)
    {
        $this->deleteObject($object, $this->provider->getCategoryConfig());
    }

    /**
     * @param Concrete $object
     */
    public function deleteAsset(Concrete $object)
    {
        $this->deleteObject($object, $this->provider->getAssetConfig());
    }
    /**
     * @param string $url
     * @return string
     */
    protected function getUrl(string $url): string
    {
        return $this->configuration->getInstanceUrl() . $url;
    }

    /**
     * @param                $data
     * @param EndpointConfig $config
     * @return Request
     */
    protected function getPutRequest($data, EndpointConfig $config): Request
    {
        return new Request(
            SymfonyRequest::METHOD_PUT,
            $this->getUrl($config->getSendUrlParam()),
            $this->getDefaultHeaders(),
            $data
        );
    }

    /**
     * @param                $id
     * @param EndpointConfig $config
     * @return Request
     */
    protected function getDeleteRequest($id, EndpointConfig $config): Request
    {
        return new Request(
            SymfonyRequest::METHOD_DELETE,
            $this->getUrl($config->getDeleteUrlparam()) . $id,
            $this->getDefaultHeaders()
        );
    }

    /**
     * @param $url
     * @return Request
     */
    protected function getGetReqest($url): Request
    {
        return new Request(
            SymfonyRequest::METHOD_GET,
            $this->getUrl($url),
            $this->defaultHeaders
        );
    }

    /**
     * @param Concrete       $object
     * @param EndpointConfig $config
     */
    protected function deleteObject(Concrete $object, EndpointConfig $config): void
    {
        try {
            $promise = $this->client->sendAsync($this->getDeleteRequest($object->getId(), $config))
                ->then(function ($response) use ($object) {
                    if ($response->getStatusCode() > 204) {
                        Simple::log('magento2-connector/rest-client',
                            "Could not send data to remote service. Response: " . $response->getBody()
                        );
                        $this->logError("Could not send data to remote service.", $object);
                    }
                });
            $promise->wait();
        } catch (Throwable $throwable) {
            Simple::log('magento2-connector/rest-client', $throwable->getMessage());
            $this->logError("Could not send data to remote service.", $object, $throwable);
        }
    }

    /**
     * @param Asset $asset
     */
    public function sendModifiedAsset(Asset $asset): void
    {
        $payload     = [
            $this->provider->getAssetConfig()->getPayloadAttribute() => $asset->getId(),
            'store_view_id'                => $this->configuration->getMagentoStore()
        ];
        $encodedData = json_encode(['data' => $payload], JSON_FORCE_OBJECT);
        try {
            $promise = $this->client->sendAsync($this->getPutRequest($encodedData, $this->provider->getAssetConfig()))
                ->then(function ($response) use ($asset) {
                    if ($response->getStatusCode() > 204) {
                        Simple::log('magento2-connector/rest-client',
                            "Could not send data to remote service. Response: " . $response->getBody()
                        );
                        $this->logError("Could not send data to remote service.", $asset);
                    }
                });
            $promise->wait();
        } catch (Throwable $throwable) {
            Simple::log('magento2-connector/rest-client', $throwable->getMessage());
            $this->logError("Could not send data to remote service.", $asset, $throwable);
        }
    }

    /**
     * @param Concrete $object
     */
    protected function sendObject(Concrete $object, EndpointConfig $config): void
    {
        $payload     = [
             $config->getPayloadAttribute() => $object->getId(),
            'store_view_id' => $this->configuration->getMagentoStore()
        ];

        $encodedData = json_encode(['data' => $payload], JSON_FORCE_OBJECT);
        try {
            $promise = $this->client->sendAsync($this->getPutRequest($encodedData, $config))
                ->then(function ($response) use ($object) {
                    if ($response->getStatusCode() > 204) {
                        Simple::log('magento2-connector/rest-client',
                            "[ERROR] Could not send data to remote service. Response: " . $response->getBody()
                        );
                        $this->logError("[ERROR] Could not send data to remote service.", $object);
                    } else {
                        Simple::log('magento2-connector/rest-client',
                            "[DEBUG]: " . $response->getBody()
                        );
                    }
                });
            $promise->wait();
        } catch (Throwable $throwable) {
            Simple::log('magento2-connector/rest-client', $throwable->getMessage());
            $this->logError("[ERROR] Could not send data to remote service.", $object, $throwable);
        }
    }

 /**
  * @param                      $msg
  * @param AbstractElement|null $object
  * @param \Throwable|null      $exception
  */
    protected function logError($msg, AbstractElement $object = null, \Throwable $exception = null): void
    {
        $msg = $object !== null ? $msg . " Object: " . $object->getId() : $msg;
        if (!$exception == null) {
            $msg = $msg . " " . $exception->getMessage();
        }
        $this->logger->error($msg);
    }

    /**
     * @return mixed
     */
    public function getStores()
    {
        $data = $this->getGetReqest($this->provider->getStoreViewsEndpointUrl());

        try {
            $promise = $this->client->sendAsync($data)
                ->then(function ($response) {
                    if ($response->getStatusCode() > 204) {
                        $this->logError(
                            "Could not get stores list from "
                            . $this->getUrl($this->provider->getStoreViewsEndpointUrl())
                        );
                        Simple::log('magento2-connector/rest-client',
                            "Could not get stores list from "
                            . $this->getUrl($this->provider->getStoreViewsEndpointUrl())
                        );
                    }
                    return json_decode($response->getBody()->getContents()) ?? [];
                }, function () {
                    $this->logError(
                        "Could not get stores list from "
                        . $this->getUrl($this->provider->getStoreViewsEndpointUrl())
                    );
                    Simple::log('magento2-connector/rest-client',
                        "Could not get stores list from "
                        . $this->getUrl($this->provider->getStoreViewsEndpointUrl())
                    );
                    return [];
                });
            $value = $promise->wait();
            return is_array($value) ? $value : [];
        } catch (Throwable $throwable) {
            $this->logError(
                "Could not get stores list from "
                . $this->getUrl($this->provider->getStoreViewsEndpointUrl())
            );
            Simple::log('magento2-connector/rest-client',
                "Could not get stores list from "
                . $this->getUrl($this->provider->getStoreViewsEndpointUrl())
            );
            return [];
        }
    }
}
