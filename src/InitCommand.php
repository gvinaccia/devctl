<?php

namespace GVinaccia\DevCtl;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $process = new Process(sprintf('sh %s/scripts/_devctl_init.sh', realpath(__DIR__.'/..')));
        $process->run();
        $output->write($process->getOutput());
    }
}
