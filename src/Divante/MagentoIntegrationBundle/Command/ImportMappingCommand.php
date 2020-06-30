<?php

namespace Divante\MagentoIntegrationBundle\Command;

use Divante\MagentoIntegrationBundle\Application\Mapper\MapperImporter;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImportMappingCommand
 * @package Divante\MagentoIntegrationBundle\Command
 */
class ImportMappingCommand extends AbstractCommand
{
    /**
     * @var MapperImporter
     */
    protected $mapperImporter;

    /**
     * @var string
     */
    protected static $defaultName = 'integration-magento:import-mappings';

    /**
     * ImportMappingCommand constructor.
     * @param MapperImporter $mapperImporter
     * @param string|null $name
     */
    public function __construct(MapperImporter $mapperImporter, string $name = null)
    {
        parent::__construct($name);
        $this->mapperImporter = $mapperImporter;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Import product or category mappings to selected configuration object');

        $this->addArgument(
            "idConfiguration",
            InputArgument::REQUIRED,
            "Id of selected integration configuration, you want to import mappings"
        );

        $this->addArgument(
            "type",
            InputArgument::REQUIRED,
            "Type 'product' or 'category' mapping"
        );

        $this->addArgument(
            "path",
            InputArgument::REQUIRED,
            "Path to json file"
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
        $idConfig = $input->getArgument("idConfiguration");
        $type = $input->getArgument("type");
        $filePath = $input->getArgument("path");

        $file = new UploadedFile($filePath, "mappings");
        try {
            $this->mapperImporter->importMappingData($idConfig, $type, $file);
            $output->writeln("<fg=green>Import Succeed!</>");
        } catch (\Exception $exception) {
            $errorMsg = $exception->getMessage();
            $output->writeln("<fg=red>Import Failure!</>");
            $output->writeln("<fg=red>Message: " . $errorMsg . "</>");
        }

        $timeElapsed = microtime(true) - $start;

        $output->writeln(sprintf("<fg=green>Execution time : %.2f seconds</>", $timeElapsed));

        return 0;
    }
}
