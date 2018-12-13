<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        20/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Service;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Interfaces\MapperInterface;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Model\Request\AbstractObjectRequest;
use Divante\MagentoIntegrationBundle\Model\Request\UpdateStatus;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Webservice\Data\Mapper;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class AbstractObjectService
 * @package Divante\MagentoIntegrationBundle\Service
 */
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractObjectService
 * @package Divante\MagentoIntegrationBundle\Service
 */
abstract class AbstractObjectService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var ApplicationLogger  */
    private $logger;

    /** @var TokenStorageUserResolver  */
    private $userResolver;

    /** @var IntegrationConfigurationService  */
    protected $integrationService;
    /** @var MapperInterface  */
    protected $mapper;

    /** @var EventDispatcher */
    protected $eventDispatcher;

    /**
     * ProductService constructor.
     * @param MapperInterface          $mapper
     * @param ContainerInterface       $container
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        MapperInterface $mapper,
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->mapper = $mapper;
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
        DataObject\AbstractObject::setGetInheritedValues(true);
    }

    /**
     * @return MapperInterface
     */
    protected function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * @return ApplicationLogger
     */
    protected function getLogger(): ApplicationLogger
    {
        if (!$this->logger instanceof ApplicationLogger) {
            $this->logger = $this->container->get(ApplicationLogger::class);
        }
        return $this->logger;
    }

    /**
     * @return TokenStorageUserResolver
     */
    protected function getUserResolver(): TokenStorageUserResolver
    {
        if (!$this->userResolver instanceof TokenStorageUserResolver) {
            $this->userResolver = $this->container->get(TokenStorageUserResolver::class);
        }
        return $this->userResolver;
    }

    /**
     * @return IntegrationConfigurationService
     */
    protected function getIntegrationService(): IntegrationConfigurationService
    {
        if (!$this->integrationService instanceof IntegrationConfigurationService) {
            $this->integrationService = $this->container->get(IntegrationConfigurationService::class);
        }
        return $this->integrationService;
    }

    /**
     * @return array
     */
    protected function getOkResponse(): array
    {
        return [
            'success' => true,
            'msg' => null
        ];
    }

    /**
     * @param AbstractObjectRequest $objectRequest
     * @param null                  $id
     * @return array
     */
    protected function getNotFoundResponse(AbstractObjectRequest $objectRequest, $id = null): array
    {
        $responseElementId = $id ?? $objectRequest->id;
        return [
            'success' => false,
            'msg' => $this->getNotFoundMessage($responseElementId)
        ];
    }

    /**
     * @param null $id
     * @return string
     */
    protected function getNotFoundMessage($id = null): string
    {
        return sprintf('Requested object with id %d does not exist.', $id);
    }

    /**
     * @param string $msg
     * @return string
     */
    protected function getLoggedErrorMessage(string $msg): string
    {
        $this->getLogger()->error($msg);
        return $msg;
    }

    /**
     * @param AbstractObjectRequest $objectRequest
     * @param null                  $id
     * @return array
     */
    protected function getLoggedNotFoundResponse(AbstractObjectRequest $objectRequest, $id = null): array
    {
        $data = $this->getNotFoundResponse($objectRequest, $id);
        $this->getLogger()->error($data['msg']);
        return $data;
    }

    /**
     * @param AbstractElement $element
     * @return bool
     * @throws \Exception
     */
    protected function checkObjectPermission(AbstractElement $element)
    {
        $isAllowed = $element->isAllowed('view');
        if (!$isAllowed) {
            $this->getLogger()->error(
                'User {user} attempted to access {permission} on {elementType} {elementId},'
                . 'but has no permission to do so',
                [
                    'user'        => $this->getAdminUser()->getName(),
                    'permission'  => 'view',
                    'elementType' => $element->getType(),
                    'elementId'   => $element->getId(),
                ]
            );
            throw new \Exception('You have not permission to this element');
        }
        return $isAllowed;
    }

    /**
     * Get user from user proxy object which is registered on security component
     *
     * @param bool $proxyUser Return the proxy user (UserInterface) instead of the pimcore model
     *
     * @return null|\Pimcore\Bundle\AdminBundle\Security\User\User|\Pimcore\Model\User
     */
    protected function getAdminUser($proxyUser = false)
    {
        if ($proxyUser) {
            return $this->getUserResolver()->getUserProxy();
        }
        return $this->getUserResolver()->getUser();
    }

    /**
     * @param $id
     * @return DataObject\Listing
     */
    protected function loadObjects($id): DataObject\Listing
    {
        $listing = new DataObject\Listing();
        $ids = $this->filterIds($id);
        if (!$ids) {
            $listing->setCondition("false");
        } else {
            $listing->setCondition(sprintf("o_id IN (%s)", implode(', ', array_fill(0, count($ids), '?'))), $ids);
            $listing->setObjectTypes(
                [DataObject\AbstractObject::OBJECT_TYPE_VARIANT, DataObject\AbstractObject::OBJECT_TYPE_OBJECT]
            );
        }
        $listing->load();
        return $listing;
    }

    /**
     * @param string $ids
     * @return array
     */
    protected function filterIds(string $ids): array
    {
        $ids = explode(',', $ids);
        $ids = array_filter($ids, function ($elem) {
            return is_numeric($elem);
        });
        return $ids;
    }

    /**
     * @param DataObject\Concrete   $object
     * @param AbstractObjectRequest $request
     * @return IntegrationConfiguration
     * @throws \Exception
     */
    protected function getConfigurationForObject(
        DataObject\Concrete $object,
        AbstractObjectRequest $request
    ): IntegrationConfiguration {
        return $this->getIntegrationService()->getFirstConfiguration(
            $object,
            $request->instaceUrl,
            $request->storeViewId
        );
    }

    /**
     * @param DataObject\Concrete $object
     * @return mixed
     * @throws \Exception
     */
    public function getOutObject(DataObject\Concrete $object)
    {

        // load all data (eg. lazy loaded fields like multihref, object, ...)
        DataObject\Service::loadAllObjectFields($object);
        $apiObject = Mapper::map($object, '\\Pimcore\\Model\\Webservice\\Data\\DataObject\\Concrete\\Out', 'out');

        return $apiObject;
    }

    /**
     * @param                       $idArray
     * @param AbstractObjectRequest $request
     * @return array
     */
    protected function getMissingIds($idArray, AbstractObjectRequest $request): array
    {
        $missingData = [];

        foreach (explode(',', $request->id) as $id) {
            if (!in_array($id, $idArray)) {
                $missingData[$id] = $this->getLoggedErrorMessage($this->getNotFoundMessage($id));
            }
        }
        return $missingData;
    }

    /**
     * @param AbstractElement $object
     * @param UpdateStatus    $updateObject
     */
    protected function logSyncStatus(AbstractElement $object, UpdateStatus $updateObject): void
    {
        switch ($updateObject->status) {
            case (IntegrationHelper::SYNC_STATUS_ERROR):
                $this->getLogger()->warning(
                    'Error while syncing object: {id} with instance: {instance} for store view: {storeViewId}. '
                    . 'Message: {message}',
                    [
                        'id'          => $updateObject->id,
                        'instance'    => $updateObject->instaceUrl,
                        'storeViewId' => $updateObject->storeViewId,
                        'message'     => $updateObject->message
                    ]
                );
                $this->addNote(
                    $object,
                    $updateObject,
                    'warning',
                    'Error while syncing data with Magento.'
                );
                break;
            case (IntegrationHelper::SYNC_STATUS_OK):
                $this->addNote(
                    $object,
                    $updateObject,
                    'notice',
                    'Product has been synchronized with Magento.'
                );
                break;
            case (IntegrationHelper::SYNC_STATUS_DELETE):
                $this->addNote(
                    $object,
                    $updateObject,
                    'notice',
                    'Product has been removed from Magento.'
                );
                break;
        }
    }

    /**
     * @param AbstractElement     $object
     * @param UpdateStatus $updateObject
     * @param string       $type
     * @param string       $title
     */
    private function addNote(AbstractElement $object, UpdateStatus $updateObject, string $type, string $title): void
    {
        $note = new Note();
        $note->setElement($object);
        $note->setType($type);
        $note->setTitle($title);
        $note->setDescription($updateObject->message);
        $note->addData('Instance', 'text', $updateObject->instaceUrl);
        $note->addData('Store view id', 'text', $updateObject->storeViewId);
        $note->setDate(time());
        $note->save();
    }
}
