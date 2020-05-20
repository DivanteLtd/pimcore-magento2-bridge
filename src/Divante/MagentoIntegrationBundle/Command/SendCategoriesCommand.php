<?php

namespace Divante\MagentoIntegrationBundle\Command;

use Divante\MagentoIntegrationBundle\Application\BulkAction\BulkUpdateService;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/*
 * Class SendCategoriesCommand
 * @package Divante\MagentoIntegrationBundle\Command
 */
class SendCategoriesCommand extends AbstractCommand
{

    private $updateSerice;
    protected static $defaultName = 'integration-magento:send:category';

    /**
     * SendCategoriesCommand constructor.
     * @param BulkUpdateService $bulkUpdateService
     * @param string|null       $name
     */
    public function __construct(BulkUpdateService $bulkUpdateService, string $name = null)
    {
        parent::__construct($name);
        $this->updateSerice = $bulkUpdateService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Sends category or categories if (all) that fulfill integration configuration');

        $this->addArgument(
            "idCategory",
            InputArgument::REQUIRED,
            "Id or comma separated ids of produ180cts you want to send or 'all' if you want to send all of them"
        );

        $this->addArgument(
            "idConfiguration",
            InputArgument::REQUIRED,
            "id of integration configuration object you want use to send product(s)"
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $idCategory = $input->getArgument("idCategory");
        $idConfig = $input->getArgument("idConfiguration");
        $this->updateSerice->setLogger(new ConsoleLogger($output));
        $objects = $this->updateSerice->updateCategories($idCategory, $idConfig);

        $timeElapsed = microtime(true) - $start;
        $output->writeln("<fg=green>Send Categories command has succeeded.</>");
        $output->writeln(sprintf("<fg=green>Execution time : %.2f seconds</>", $timeElapsed));
        if ($input->getOption("verbose")) {
            $output->writeln("<fg=yellow>Verbose option output</>");
            foreach ($objects as $key => $object) {
                $output->writeln(sprintf(
                    "%s. classname:%s, id:%s, path:'%s'",
                    $key+1,
                    $object->getClassName(),
                    $object->getId(),
                    $object->getFullPath()
                ));
            }
        }

        return 0;
    }
}
