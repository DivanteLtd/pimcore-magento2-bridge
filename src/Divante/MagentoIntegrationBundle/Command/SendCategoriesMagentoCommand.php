<?php

namespace Divante\MagentoIntegrationBundle\Command;

use Divante\MagentoIntegrationBundle\Domain\Admin\SendCategoriesService;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class SendCategoriesMagentoCommand
 * @package Divante\MagentoIntegrationBundle\Command
 */
class SendCategoriesMagentoCommand extends AbstractCommand
{
    /** @var SendCategoriesService */
    private $sendCategoriesAction;

    /**
     * SendCategoriesMagentoCommand constructor.
     * @param SendCategoriesService $sendCategoriesAction
     * @param string|null $name
     */
    public function __construct(SendCategoriesService $sendCategoriesAction, string $name = null)
    {
        parent::__construct($name);
        $this->sendCategoriesAction = $sendCategoriesAction;
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
            "id of product you want to send or 'all' if you want to send all of them"
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
     * @throws ConnectionException
     * @throws UserDeletionFailed
     * @throws InactiveUserListingException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $idCategory = $input->getArgument("idCategory");
        $idConfig = $input->getArgument("idConfiguration");

        $products = $this->sendCategoriesAction->sendObjects($idCategory, $idConfig);

        $output->writeln("<fg=green>Success</>");
        $output->writeln(count($products) . " category(ies) sent!");

        return 0;
    }
}
