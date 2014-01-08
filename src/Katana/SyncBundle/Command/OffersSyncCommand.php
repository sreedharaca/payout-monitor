<?php

namespace Katana\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OffersSyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('offers:sync')
            ->setDescription('offers sync')
            /*->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )*/
            /*->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )*/
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $UO = $this->getContainer()->get('offers_update');
        $UO->startSyncProcess();

        //$output->writeln('run command update');
    }


}