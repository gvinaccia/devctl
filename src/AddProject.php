<?php

namespace GVinaccia\DevCtl;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class AddProject extends Command
{
    protected function configure()
    {
        $this->setName('project:add')
             ->addOption('url', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getOption('url');

        if ($url === null) {
            $output->writeln('è necessario specificare un url per il progetto');
            return 1;
        }

        $currentDir = getcwd();

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
        $config = str_replace('__ROOT__', $currentDir . '/public', $config);
        $config = str_replace('__SERVER_NAME__', $url, $config);

        $configFile = getenv('HOME')  . '/dev/conf/sites-enabled/' . basename($currentDir) . '.conf';

        file_put_contents($configFile, $config);

        $output->writeln("configurazione scritta in $configFile");
    }
}