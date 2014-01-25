<?php

namespace Katana\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OffersResolveAppCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('offers:resolveapp')
            ->setDescription('tie offers to app')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $UO = $this->getContainer()->get('offers_update');
        $UO->tieOffersToApp();
    }


}