<?php

namespace Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy\Custom;

/**
 * Interface CustomStrategyInterface
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Strategy\Custom
 */
interface CustomStrategyInterface
{
    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return string
     */
    public function getIdentifier(): string;
}
