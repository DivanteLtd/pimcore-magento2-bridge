<?php

namespace Divante\MagentoIntegrationBundle\Domain\Admin;

use Divante\MagentoIntegrationBundle\Domain\Admin\Request\GetIntegrationConfiguration;
use Symfony\Component\Process\Process;

/**
 * Class SendCommandExcecutor
 */
class CommandExcecutor
{
    /**
     * @param string $idConfiguration
     */
    public function excecuteCommandSendCategories(GetIntegrationConfiguration $query)
    {
        $this->excecuteCommand(sprintf("integration-magento:send:category all %s", $query->id));
    }

    /**
     * @param string $idConfiguration
     */
    public function excecuteCommandSendProducts(GetIntegrationConfiguration $query)
    {
        $this->excecuteCommand(sprintf("integration-magento:send:product all %s", $query->id));
    }

    /**
     * @param string $command
     */
    private function excecuteCommand(string $command)
    {
        $process = new Process(sprintf("bin/console %s", $command));
        $process->start();
    }
}
