<?php

namespace Divante\MagentoIntegrationBundle\Application\Integration\Magento;

use Divante\MagentoIntegrationBundle\Domain\Common\ObjectTypeHelper;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\Model\EndpointConfig;
use Divante\MagentoIntegrationBundle\Domain\Notification\Event\DeleteNotificationFailedEvent;
use Divante\MagentoIntegrationBundle\Domain\Notification\Event\DeleteNotificationSuccededEvent;
use Divante\MagentoIntegrationBundle\Domain\Notification\Event\UpdateNotificationFailedEvent;
use Divante\MagentoIntegrationBundle\Domain\Notification\Event\UpdateNotificationSuccededEvent;
use Divante\MagentoIntegrationBundle\Domain\Provider\RestOutputProviderInterface;
use Divante\MagentoIntegrationBundle\Infrastructure\Rest\RequestClient;
use GuzzleHttp\Exception\GuzzleException;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MagentoNotificationSender
 */
class MagentoNotificationSender
{
    /** @var RestOutputProviderInterface */
    private $urlProvider;
    /** @var RequestClient */
    private $client;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * MagentoNotificationSender constructor.
     * @param RestOutputProviderInterface $urlProvider
     * @param RequestClient               $client
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(
        RestOutputProviderInterface $urlProvider,
        RequestClient $client,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->urlProvider = $urlProvider;
        $this->client = $client;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Concrete $object
     * @param IntegrationConfiguration $configuration
     */
    public function sendProductUpdate(Concrete $object, IntegrationConfiguration $configuration)
    {
        $config = $this->urlProvider->getProductConfig();
        $this->sendUpdateNotification($object, $configuration, $config, ObjectTypeHelper::PRODUCT);
    }

    /**
     * @param Concrete $object
     * @param IntegrationConfiguration $configuration
     */
    public function sendProductDelete(Concrete $object, IntegrationConfiguration $configuration)
    {
        $config = $this->urlProvider->getProductConfig();
        $this->sendDeleteNotification($object, $configuration, $config, ObjectTypeHelper::PRODUCT);
    }

    /**
     * @param Concrete $object
     * @param IntegrationConfiguration $configuration
     */
    public function sendCategoryUpdate(Concrete $object, IntegrationConfiguration $configuration)
    {
        $config = $this->urlProvider->getCategoryConfig();
        $this->sendUpdateNotification($object, $configuration, $config, ObjectTypeHelper::CATEGORY);
    }

    /**
     * @param Concrete $object
     * @param IntegrationConfiguration $configuration
     */
    public function sendCategoryDelete(Concrete $object, IntegrationConfiguration $configuration)
    {
        $config = $this->urlProvider->getCategoryConfig();
        $this->sendDeleteNotification($object, $configuration, $config, ObjectTypeHelper::CATEGORY);
    }

    /**
     * @param AbstractElement          $object
     * @param IntegrationConfiguration $configuration
     */
    public function sendAssetUpdate(AbstractElement $object, IntegrationConfiguration $configuration)
    {
        $config = $this->urlProvider->getAssetConfig();
        $this->sendUpdateNotification($object, $configuration, $config, ObjectTypeHelper::ASSET);
    }

    /**
     * @param AbstractElement          $object
     * @param IntegrationConfiguration $configuration
     */
    public function sendAssetDelete(AbstractElement $object, IntegrationConfiguration $configuration)
    {
        $config = $this->urlProvider->getAssetConfig();
        $this->sendDeleteNotification($object, $configuration, $config, ObjectTypeHelper::ASSET);
    }


    /**
     * @param AbstractElement $object
     * @param IntegrationConfiguration $configuration
     * @param EndpointConfig $config
     * @param string $type
     */
    private function sendUpdateNotification(
        AbstractElement $object,
        IntegrationConfiguration $configuration,
        EndpointConfig $config,
        string $type
    ): void {
        $payload = [
            $config->getPayloadAttribute() => $object->getId(),
            "store_view_id"                => $configuration->getMagentoStore()
        ];
        $this->client
            ->setConfiguration($configuration)
            ->setMethod(Request::METHOD_PUT)
            ->setUri($config->getSendUrlParam())
            ->setQuery(json_encode([
                "data" => $payload,
            ], JSON_FORCE_OBJECT))
            ->setType($type);
        try {
            $response = $this->client->send();
            $responseData = (string) $response->getBody();
            if ($response->getStatusCode() === 200) {
                $this->eventDispatcher->dispatch(
                    new UpdateNotificationSuccededEvent($object, $configuration, $responseData)
                );
                return;
            }
        } catch (GuzzleException $exception) {
            $responseData = $exception->getMessage();
        }

        $this->eventDispatcher->dispatch(
            new UpdateNotificationFailedEvent($object, $configuration, $responseData)
        );
    }

    /**
     * @param AbstractElement $object
     * @param IntegrationConfiguration $configuration
     * @param EndpointConfig $config
     * @param string $type
     */
    private function sendDeleteNotification(
        AbstractElement $object,
        IntegrationConfiguration $configuration,
        EndpointConfig $config,
        string $type
    ): void {
        $this->client
            ->setConfiguration($configuration)
            ->setMethod(Request::METHOD_DELETE)
            ->setUri(sprintf("%s%s", $config->getDeleteUrlparam(), $object->getId()))
            ->setType($type);
        try {
            $response     = $this->client->send();
            $responseData = (string)$response->getBody();
            if ($response->getStatusCode() === 200) {
                $this->eventDispatcher->dispatch(
                    new DeleteNotificationSuccededEvent($object, $configuration, $responseData)
                );
                return;
            }
        } catch (GuzzleException $exception) {
            $responseData = $exception->getMessage();
        }
        $this->eventDispatcher->dispatch(
            new DeleteNotificationFailedEvent($object, $configuration, $responseData)
        );
    }
}
