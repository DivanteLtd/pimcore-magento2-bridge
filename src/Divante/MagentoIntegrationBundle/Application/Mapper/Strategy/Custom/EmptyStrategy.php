<?php

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\Custom;

use Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\Custom\CustomStrategyInterface;
use Pimcore\Model\Webservice\Data\DataObject\Element;

/**
 * Class EmptyStrategy
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy\Custom
 */
class EmptyStrategy implements CustomStrategyInterface
{
    /** @var string */
    public $label = "(Empty)";

    /** @var string */
    public $identifier = "";

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
}
