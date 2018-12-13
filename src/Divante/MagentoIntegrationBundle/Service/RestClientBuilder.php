<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        12/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service;

use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;

/**
 * Class RestClientBuilder
 * @package Divante\MagentoIntegrationBundle\Service
 */
class RestClientBuilder
{
    /** @var RestClient */
    protected $restClient;

    protected $helperFactory;

    /**
     * RestClientBuilder constructor.
     * @param RestClient $restClient
     */
    public function __construct(RestClient $restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return RestClient
     */
    public function getClient(IntegrationConfiguration $configuration)
    {
        if (!$configuration->getClientSecret() || !$configuration->getInstanceUrl()) {
            return null;
        }
        $client = $this->restClient;
        $client->setConfiguration($configuration);
        return $client;
    }
}
