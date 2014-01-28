<?php

namespace Katana\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CurlRedirectCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('curl:redirect')
            ->setDescription("offers resolves offers' final url")
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'What Url you want test on redirect?'
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
        $Curl = $this->getContainer()->get('CurlService');

        $url = $input->getArgument('url');

        $finalUrl = $Curl->catchRedirectUrl($url);

        $output->writeln("Start Url is: $url");
        $output->writeln("Final Url is: $finalUrl");
    }


}