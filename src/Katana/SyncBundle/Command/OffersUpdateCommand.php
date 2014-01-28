<?php

namespace Katana\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OffersUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('offers:update')
            ->setDescription('offers update')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $UO = $this->getContainer()->get('offers_update');
        $UO->updateOffers();
    }


}