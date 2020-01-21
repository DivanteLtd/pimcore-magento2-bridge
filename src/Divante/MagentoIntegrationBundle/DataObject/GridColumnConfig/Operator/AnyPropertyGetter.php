<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\DataObject\GridColumnConfig\Operator;

use Pimcore\DataObject\GridColumnConfig\Operator\AbstractOperator;

/**
 * Class AnyPropertyGetter
 * @package Divante\MagentoIntegrationBundle\DataObject\GridColumnConfig\Operator
 */
class AnyPropertyGetter extends AbstractOperator
{
    private $propertyName;

    /**
     * AnyPropertyGetter constructor.
     * @param \stdClass $config
     * @param null      $context
     */
    public function __construct(\stdClass $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->propertyName = $config->propertyName;
    }

    /**
     * @param \Pimcore\Model\Element\ElementInterface $element
     * @return null|\stdClass
     */
    public function getLabeledValue($element)
    {
        $result        = new \stdClass();
        $result->label = $this->label;
        $properties    = $element->getProperties();
        if (array_key_exists($this->getPropertyName(), $properties)) {
            $result->value = $properties[$this->getPropertyName()]->getData();
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @param mixed $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }
}
