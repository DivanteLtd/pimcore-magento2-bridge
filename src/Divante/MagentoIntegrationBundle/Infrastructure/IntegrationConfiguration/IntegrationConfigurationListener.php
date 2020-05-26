<?php
/**
 * @category    pimcore
 * @date        26/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Infrastructure\IntegrationConfiguration;

use Divante\MagentoIntegrationBundle\Application\DataObject\RelationClassCollector;
use Divante\MagentoIntegrationBundle\Application\IntegrationConfiguration\IntegrationConfigurationValidator;
use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration\IntegrationHelper;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ValidationException;
use Pimcore\Model\WebsiteSetting;

/**
 * Class IntegrationConfigurationListener
 * @package Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration
 */
class IntegrationConfigurationListener
{
    /** @var IntegrationConfigurationValidator */
    private $validator;

    /** @var RelationClassCollector */
    private $relationClassCollector;

    /**
     * IntegrationConfigurationListener constructor.
     * @param IntegrationConfigurationValidator $validator
     * @param RelationClassCollector $relationClassCollector
     */
    public function __construct(
        IntegrationConfigurationValidator $validator,
        RelationClassCollector $relationClassCollector
    ) {
        $this->validator = $validator;
        $this->relationClassCollector = $relationClassCollector;
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

        $allowedRelationClasses = $this->relationClassCollector->getRelatedClassFromConfigurations();

        $websiteConfig = WebsiteSetting::getByName(IntegrationHelper::WEBSITE_SETTINGS_ALLOWED_CLASSES);
        if (!$websiteConfig) {
            $websiteConfig = new WebsiteSetting();
            $websiteConfig->setName(IntegrationHelper::WEBSITE_SETTINGS_ALLOWED_CLASSES);
            $websiteConfig->setType("text");
        }

        $websiteConfig->setData(json_encode($allowedRelationClasses));
        $websiteConfig->save();
    }
}
