<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\Common;

use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\AbstractElement;

/**
 * Interface IntegratedElementServiceInterface
 * @package Divante\MagentoIntegrationBundle\Domain\Common
 */
interface IntegratedElementServiceInterface
{
    public function supports(AbstractElement $element, IntegrationConfiguration $configuration): bool;
    public function send(AbstractElement $element, IntegrationConfiguration $configuration): void;
    public function delete(AbstractElement $element, IntegrationConfiguration $configuration): void;
    public function validate(AbstractElement $element, IntegrationConfiguration $configuration): void;
    public function setSendStatus(AbstractElement $element, IntegrationConfiguration $configuration): void;
    public function setDeleteStatus(AbstractElement $element, IntegrationConfiguration $configuration): void;
}
