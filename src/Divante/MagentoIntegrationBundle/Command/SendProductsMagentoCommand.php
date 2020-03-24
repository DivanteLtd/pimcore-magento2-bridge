<?php

namespace Divante\MagentoIntegrationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Pimcore\Console\AbstractCommand;
use Divante\MagentoIntegrationBundle\Command\Service\SendProductsService;

/**
 * Class SendProductsMagentoCommand
 */
class SendProductsMagentoCommand extends AbstractCommand
{
    /** @var SendProductsService */
    private $sendProductService;

    /**
     * SendProductsMagentoCommand constructor.
     * @param SendProductsService $sendProductService
     * @param string|null $name
     */
    public function __construct(SendProductsService $sendProductService, string $name = null)
    {
        parent::__construct($name);
        $this->sendProductService = $sendProductService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Sends product or products if (--all) that fulfill integration configuration');

        $this->addArgument(
            "idProduct",
            InputArgument::REQUIRED,
            "id of product you want to send or '--all' if you want to send all of them"
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
        $idProduct = $input->getArgument("idProduct");
        $idConfig = $input->getArgument("idConfifuration");

        $this->sendProductService->sendProducts($idProduct, $idConfig);

        return 0;
    }

}
