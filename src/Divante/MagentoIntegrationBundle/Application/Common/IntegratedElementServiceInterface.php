<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Common;

use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;

/**
 * Interface IntegratedElementServiceInterface
 * @package Divante\MagentoIntegrationBundle\Domain\Common
 */
interface IntegratedElementServiceInterface
{
    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     * @return bool
     */
    public function supports(AbstractElement $element, IntegrationConfiguration $configuration): bool;

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function send(AbstractElement $element, IntegrationConfiguration $configuration): void;

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function delete(AbstractElement $element, IntegrationConfiguration $configuration): void;

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function validate(AbstractElement $element, IntegrationConfiguration $configuration): void;

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function setSendStatus(AbstractElement $element, IntegrationConfiguration $configuration): void;

    /**
     * @param AbstractElement          $element
     * @param IntegrationConfiguration $configuration
     */
    public function setDeleteStatus(AbstractElement $element, IntegrationConfiguration $configuration): void;
}
