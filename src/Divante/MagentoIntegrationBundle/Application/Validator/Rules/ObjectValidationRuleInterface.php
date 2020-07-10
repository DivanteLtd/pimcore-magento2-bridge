<?php

namespace Divante\MagentoIntegrationBundle\Application\Validator\Rules;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\IntegrationConfiguration;

/**
 * Interface ObjectValidatornterface
 * @package Divante\MagentoIntegrationBundle\Application\Validator\Rules
 */
interface ObjectValidationRuleInterface
{
    /**
     * @param AbstractObject $object
     * @param IntegrationConfiguration $configuration
     * @param string $type
     * @throws \Exception
     */
    public function isValid(AbstractObject $object, IntegrationConfiguration $configuration, string $type);

    /**
     * Returns Validator priority 0 - highest
     * @return int
     */
    public static function getPriority(): int;

    /**
     * @return bool
     */
    public function isSilent(): bool;
}
