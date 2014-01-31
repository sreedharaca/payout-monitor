<?php

namespace Katana\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuzzGetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('buzz:get')
            ->setDescription("")
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'What Url you want request?'
            )
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
        $buzz = $this->getContainer()->get('buzz.browser');

        $url = $input->getArgument('url');

        echo $buzz->get($url);

//        $output->writeln("Start Url is: $url");
//        $output->writeln("Final Url is: $finalUrl");
    }


}