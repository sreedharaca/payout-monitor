<?php

namespace Katana\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class resolvePlatformCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('offers:platform')
            ->setDescription('Resolve Platform for each offer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $OffersService = $this->getContainer()->get('offers_update');
        $OffersService->updateOffersPlatform();
    }


}