<?php

namespace Katana\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('test:run')
            ->setDescription('import run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $test = $this->getContainer()->get('test');
        $test->run();
    }


}