<?php

namespace GVinaccia\DevCtl;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListProjects extends Command
{
    protected function configure()
    {
        $this->setName('project:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseDir = getenv('HOME').'/dev/conf/sites-enabled';

        $dir = dir($baseDir);

        $files = [];

        while ($file = $dir->read()) {
            if (is_file($baseDir.'/'.$file)) {
                $files[] = $baseDir.'/'.$file;
            }
        }

        $projects = array_filter(array_map([$this, 'parseUrl'], $files));

        foreach ($projects as $p) {
            $output->writeln($p);
        }
    }

    protected function parseUrl($f)
    {
        $matches = [];

        preg_match('/server_name\s+([\w\.]+);/', file_get_contents($f), $matches);

        return isset($matches[1]) ? 'http://' . $matches[1] : null;
    }

}
