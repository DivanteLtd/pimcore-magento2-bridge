<?php

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\Custom;

use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class JSONStrategy
 * @package Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\Custom
 */
class JSONStrategy extends AbstractCustomStrategy implements CustomStrategyInterface
{

    /** @var string */
    public $label = "Test";

    /** @var string */
    public $identifier = "test";

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function map(Element $field, \stdClass &$obj, array $arrayMapping, $language, $definition, $className)
    {
        // TODO: Implement map() method.
    }
}
