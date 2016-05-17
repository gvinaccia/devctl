<?php

namespace GVinaccia\DevCtl;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddProject extends Command
{
    protected function configure()
    {
        $this->setName('project:add')
             ->addOption('url', null, InputOption::VALUE_REQUIRED)
             ->addOption('publicDir', null, InputOption::VALUE_REQUIRED, '', 'public');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getOption('url');
        $publicDir = $input->getOption('publicDir');

        if ($url === null) {
            $output->writeln('è necessario specificare un url per il progetto');

            return 1;
        }

        $currentDir = getcwd();
        $configFile = getenv('HOME').'/dev/conf/sites-enabled/'.basename($currentDir).'.conf';

        $this->writeVHostConfig($currentDir, $publicDir, $url, $configFile);

        $output->writeln("configurazione scritta in $configFile");
    }

    /**
     * @param $currentDir
     * @param $publicDir
     * @param $url
     * @param $configFile
     */
    protected function writeVHostConfig($currentDir, $publicDir, $url, $configFile)
    {
        $config = <<<EOT
server {
    listen 80;
    root __ROOT__;
    server_name __SERVER_NAME__;
    
    index index.php index.html index.htm;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        try_files \$uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOT;
        $config = str_replace('__ROOT__', $currentDir.'/'.$publicDir, $config);
        $config = str_replace('__SERVER_NAME__', $url, $config);

        file_put_contents($configFile, $config);
    }
}
