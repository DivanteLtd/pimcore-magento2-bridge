<?php

namespace Divante\MagentoIntegrationBundle\Application\Mapper\Strategy\Custom;

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
