<?php

namespace Katana\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('offers:load')
            ->setDescription('Load all affiliates data')
            ->addArgument(
                'affiliate_id',
                InputArgument::OPTIONAL,
                'Affiliate id'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $affiliate_id = $input->getArgument('affiliate_id');

        $UO = $this->getContainer()->get('offers_update');
        $UO->jsonApiToDb($affiliate_id);
    }


}