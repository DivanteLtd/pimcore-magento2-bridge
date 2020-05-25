<?php

namespace Divante\MagentoIntegrationBundle\Application\BulkAction;

use Divante\MagentoIntegrationBundle\Infrastructure\BulkAction\OnKernelTerminateSubscriber;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

/**
 * Class BulkActionCommandExcecutor
 */
class BulkActionCommandExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var OnKernelTerminateSubscriber */
    private $eventSubscriber;

    /**
     * BulkActionCommandExecutor constructor.
     * @param OnKernelTerminateSubscriber $subscriber
     */
    public function __construct(OnKernelTerminateSubscriber $subscriber)
    {
        $this->eventSubscriber = $subscriber;
    }

    /**
     * @param string $objects
     * @param string $idConfig
     * @return void
     */
    public function executeCommandSendCategories(string $objects, string $idConfig): void
    {
        $this->eventSubscriber->setCallable(function () use ($objects, $idConfig) {
            $process = new Process(['bin/console', 'integration-magento:send:category', $objects, $idConfig]);
            $process->setWorkingDirectory(getcwd() . "/../");
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->info("Categories bulk export to Magento has been started.");
            }
            $process->start();
            $process->wait();
            if ($this->logger instanceof LoggerInterface) {
                $error = $process->getErrorOutput();
                if ($error) {
                    $this->logger->error($process->getErrorOutput());
                }
                $this->logger->info($process->getOutput());
                $this->logger->info("Categories bulk export to Magento has been finished.");
            }
        });

    }

    /**
     * @param string $objects
     * @param string $idConfig
     * @return void
     */
    public function executeCommandSendProducts(string $objects, string $idConfig): void
    {
        $this->eventSubscriber->setCallable(function () use ($objects, $idConfig) {
            $process = new Process(['bin/console', 'integration-magento:send:product', $objects, $idConfig]);
            $process->setWorkingDirectory(getcwd() . "/../");
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->info("Products bulk export to Magento has been started.");
            }
            $process->start();
            $process->wait();
            if ($this->logger instanceof LoggerInterface) {
                $error = $process->getErrorOutput();
                if ($error) {
                    $this->logger->error($process->getErrorOutput());
                }
                $this->logger->info($process->getOutput());
                $this->logger->info("Products bulk export to Magento has been finished.");
            }
        });
    }
}
