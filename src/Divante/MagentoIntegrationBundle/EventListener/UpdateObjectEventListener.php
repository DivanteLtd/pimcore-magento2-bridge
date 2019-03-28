<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        19/09/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\EventListener;

use Divante\MagentoIntegrationBundle\Helper\IntegrationHelper;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Model\Event\Update\AssetUpdateEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Update\CategoryUpdateEvent;
use Divante\MagentoIntegrationBundle\Model\Event\IntegratedObjectEvent;
use Divante\MagentoIntegrationBundle\Model\Event\Update\ProductUpdateEvent;
use Divante\MagentoIntegrationBundle\Service\RestClient;
use Divante\MagentoIntegrationBundle\Service\RestClientBuilder;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ValidationException;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\Debug\WrappedListener;

/**
 * Class UpdateObjectEventListener
 * @package Divante\MagentoIntegrationBundle\EventListener
 */
class UpdateObjectEventListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var RestClientBuilder  */
    protected $restClientBuilder;

    /**
     * DeleteObjectEventListener constructor.
     * @param RestClientBuilder $builder
     */
    public function __construct(RestClientBuilder $builder)
    {
        $this->restClientBuilder= $builder;
    }

    /**
     * @param IntegratedObjectEvent $event
     * @throws ValidationException
     */
    public function validateObject(IntegratedObjectEvent $event)
    {
        $object = $event->getObject();

        if ($event instanceof CategoryUpdateEvent) {
            /** @var Concrete $object */
            $this->validateCategory($object, $event->getConfiguration());
        } else if ($event instanceof ProductUpdateEvent) {
            $this->validateProduct($object, $event->getConfiguration());
            if ($this->isOnlyIndexChanged($object)) {
                $this->removeIntegratorListeners();
            } else {
                $this->restoreIntegratorListeners();
            }
        }
    }
    /**
     * When category is published all its parent categories must be published
     * When category is unpublished all its children needs to be unpublished
     * @param Concrete                 $object
     * @param IntegrationConfiguration $configuration
     * @throws ValidationException
     *
     */
    protected function validateCategory(
        Concrete $object,
        IntegrationConfiguration $configuration
    ): void {
        if (!$object->isPublished()) {
            return;
        }
        $parent = $object->getParent();
        $categoryRootId = $configuration->getCategoryRoot()->getId();
        while ($parent->getId() != $categoryRootId & $parent->getId() != 1) {
            if ($parent instanceof Concrete && !$parent->isPublished()) {
                throw new ValidationException("All parent categories must be before publishing children.");
            }
            $parent = $parent->getParent();
        }
    }

    /**
     * @param IntegratedObjectEvent $event
     */
    public function setSendStatus(IntegratedObjectEvent $event)
    {
        /** @var Concrete $object */
        $object = $event->getObject();
        $object->setProperty(
            IntegrationHelper::SYNC_PROPERTY_NAME,
            'text',
            IntegrationHelper::SYNC_STATUS_SENT
        );
    }

    /**
     * @param IntegratedObjectEvent $event
     */
    public function sendUpdateNotification(IntegratedObjectEvent $event)
    {
        /** @var Concrete $object */
        $object = $event->getObject();
        /** @var RestClient $client */
        $client = $this->restClientBuilder->getClient($event->getConfiguration());
        if ($event instanceof ProductUpdateEvent) {
            $client->sendProduct($object);
        } elseif ($event instanceof CategoryUpdateEvent) {
            $client->sendCategory($object);
        } elseif ($event instanceof AssetUpdateEvent) {
            $client->sendModifiedAsset($object);
        }
    }


    /**
     * Configurable products cannot be published without published variants
     * Configurable product cannot be published without specified configurable attributes
     * All products must have unique url_key
     * @param DataObject\Concrete      $element
     * @param IntegrationConfiguration $configuration
     * @throws ValidationException
     */
    protected function validateProduct(DataObject\Concrete $element, IntegrationConfiguration $configuration): void
    {
        if (!$element->isPublished()) {
            return;
        }
        AbstractObject::setHideUnpublished(true);
        $children = $element->getChildren(
            [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]
        );
        if (count($children) > 0 && !$element->hasProperty(IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE)) {
            throw new ValidationException(
                "Configurable product cannot be published without specified configurable attribute. Add property: "
                . IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE . "."
            );
        } elseif ($element->getParent() instanceof DataObject\Concrete) {
            if ($element->getParent()->isPublished() == false) {
                throw new ValidationException(
                    "To publish a variant its parent must be published."
                );
            }
            $mapping = $configuration->getDecodedProductMapping();
            $urlKeyAttrName = array_search('url_key', $mapping);
            if ($urlKeyAttrName) {
                $elementUrlKey = $element->get($urlKeyAttrName);
                if (!$elementUrlKey || $element->getParent()->get($urlKeyAttrName) == $elementUrlKey) {
                    throw new ValidationException('Variant must have unique attribute urlKey!');
                }
            }
            $this->checkConfigurableAttributesValues($element);

        }
    }

    /**
     * @param AbstractObject $object
     * @throws ValidationException
     */
    protected function checkConfigurableAttributesValues(AbstractObject $object): void
    {
        $configAttributes = $object->getProperty(IntegrationHelper::PRODUCT_TYPE_CONFIGURABLE_ATTRIBUTE);
        $attributes = explode(',', $configAttributes);
        foreach ($attributes as $attribute) {
            if (!$attribute) {
                continue;
            }
            if (strpos($attribute, '_')) {
                $attrNameArray  = explode('_', $attribute);
                if (property_exists(get_class($object), $attrNameArray[0])) {
                    continue;
                }
            }
            $value = $object->get($attribute);
            if ($value == null) {
                throw new ValidationException('Missing value for configurable attribute: ' . $attribute);
            }
        }
    }
    /**
     * @param DataObject\Concrete $object
     *
     * @return bool
     */
    protected function isOnlyIndexChanged(DataObject\Concrete $object): bool
    {
        $originObject = DataObject\Concrete::getById($object->getId(), true);
        return
            $originObject instanceof DataObject\Concrete
            && $originObject->getIndex() !== $object->getIndex()
            && $originObject->getFullPath() === $object->getFullPath();
    }


    protected function removeIntegratorListeners(): void
    {
        $integrationListener = $this->container->get(ObjectListener::class);
        $this->container->get('event_dispatcher')->removeListener(
            'pimcore.dataobject.preUpdate',
            [$integrationListener, 'onPreUpdate']
        );
        $this->container->get('event_dispatcher')->removeListener(
            'pimcore.dataobject.postUpdate',
            [$integrationListener, 'onPostUpdate']
        );
    }

    protected function restoreIntegratorListeners(): void
    {
        $listeners = $this->container->get('event_dispatcher')->getListeners('pimcore.dataobject.preUpdate');
        $exists = false;
        foreach ($listeners as $listener) {
            if ($listener instanceof WrappedListener && strpos($listener->getPretty(), ObjectListener::class) >= 0) {
                $exists = true;
            }
        }
        if (!$exists) {
            $integrationListener = $this->container->get(ObjectListener::class);
            $this->container->get('event_dispatcher')->addListener(
                'pimcore.dataobject.preUpdate',
                [$integrationListener, 'onPreUpdate']
            );
            $this->container->get('event_dispatcher')->addListener(
                'pimcore.dataobject.postUpdate',
                [$integrationListener, 'onPostUpdate']
            );
        }
    }
}
