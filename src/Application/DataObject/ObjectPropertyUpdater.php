<?php

namespace Divante\MagentoIntegrationBundle\Application\DataObject;

use Divante\MagentoIntegrationBundle\Domain\DataObject\Property\PropertyStatusHelper;
use Divante\MagentoIntegrationBundle\Infrastructure\Common\EventListenersManager;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Version;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class ObjectPropertyUpdater
 */
class ObjectPropertyUpdater implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var EventListenersManager */
    private $eventListenersManager;

    /**
     * ObjectPropertyUpdater constructor.
     * @param EventListenersManager $eventListenersManager
     */
    public function __construct(EventListenersManager $eventListenersManager)
    {
        $this->eventListenersManager = $eventListenersManager;
    }

    /**
     * @param AbstractElement $element
     * @param IntegrationConfiguration $configuration
     * @param string $status
     * @throws \Exception
     */
    public function setProperty(AbstractElement $element, IntegrationConfiguration $configuration, string $status)
    {
        $property = $element->getProperty(PropertyStatusHelper::PROPERTY_NAME);
        if ($property) {
            $data = json_decode($property, true);
        }
        if (!$data) {
            $data = [];
        }
        if (array_key_exists($this->getPropertyIndex($configuration), $data)
            && $data[$this->getPropertyIndex($configuration)] === $status) {
            return;
        }
        $data[$this->getPropertyIndex($configuration)] = $status;
        $this->saveProperty($element, $data);
    }

    /**
     * @param AbstractElement $element
     * @param array $data
     * @throws \Exception
     */
    public function saveProperty(AbstractElement $element, array $data): void
    {
        $this->eventListenersManager->disableEventListeners();
        Version::disable();
        $element->setProperty(PropertyStatusHelper::PROPERTY_NAME, "text", json_encode($data));
        if ($element instanceof Concrete) {
            $element->setOmitMandatoryCheck(true);
        }

        $element->save();
        Version::enable();
        $this->eventListenersManager->restoreEventListeners();
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    public function getProperty(AbstractElement $element): array
    {
        $properties = $element->getProperties();
        if (!array_key_exists(PropertyStatusHelper::PROPERTY_NAME, $properties)) {
            return [];
        }
        $childStatuses = $properties[PropertyStatusHelper::PROPERTY_NAME];

        return json_decode($childStatuses->getData(), true);
    }

    /**
     * @param Concrete $dbObject
     * @param Concrete $updatedObject
     * @return Concrete
     */
    public function setMagentoNotificationProperty(
        Concrete $dbObject,
        Concrete $updatedObject
    ): Concrete {
        if (!$dbObject->isPublished() && !$updatedObject->isPublished()) {
            $updatedObject->setProperty(PropertyStatusHelper::PROPERTY_NOTIFY_MAGENTO, 'bool', false);
        } else {
            $updatedObject->setProperty(PropertyStatusHelper::PROPERTY_NOTIFY_MAGENTO, 'bool', true);
        }

        return $updatedObject;
    }

    /**
     * @param IntegrationConfiguration $configuration
     * @return string
     */
    protected function getPropertyIndex(IntegrationConfiguration $configuration): string
    {
        return $configuration->getIntegrationId();
    }
}
