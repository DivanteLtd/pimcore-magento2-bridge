<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Event;

use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;

/**
 * Class PostMappingObjectEvent
 * @package Divante\MagentoIntegrationBundle\Domain\Event
 */
class PostMappingObjectEvent extends IntegratedObjectEvent
{
    /** @var \stdClass */
    private $mappedObj;

    /**
     * PostMappingObjectEvent constructor.
     * @param AbstractElement          $object
     * @param IntegrationConfiguration $configuration
     * @param \stdClass                $mappedObj
     */
    public function __construct(AbstractElement $object, IntegrationConfiguration $configuration, \stdClass $mappedObj)
    {
        parent::__construct($object, $configuration);
        $this->mappedObj = $mappedObj;
    }

    /**
     * @return \stdClass
     */
    public function getMappedObj(): \stdClass
    {
        return $this->mappedObj;
    }

    /**
     * @param \stdClass $mappedObj
     */
    public function setMappedObj(\stdClass $mappedObj): void
    {
        $this->mappedObj = $mappedObj;
    }
}
