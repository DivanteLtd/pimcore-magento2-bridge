<?php
/**
 * @category    pimcore
 * @date        26/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration;

use Divante\MagentoIntegrationBundle\Application\IntegrationConfiguration\IntegrationConfigurationValidator;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ValidationException;

/**
 * Class IntegrationConfigurationListener
 * @package Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration
 */
class IntegrationConfigurationListener
{
    /** @var IntegrationConfigurationValidator */
    private $validator;

    /**
     * IntegrationConfigurationListener constructor.
     * @param IntegrationConfigurationValidator $validator
     */
    public function __construct(IntegrationConfigurationValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param DataObjectEvent $event
     * @throws ValidationException
     */
    public function onPreUpdate(DataObjectEvent $event): void
    {
        /** @var AbstractObject $object */
        $object = $event->getObject();
        if (!$object instanceof IntegrationConfiguration || !$object->isPublished()) {
            return;
        }
        $this->validator->validate($object);
        return;
    }
}
