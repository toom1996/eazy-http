<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HttpInstallCommand extends BaseCommand
{
    protected string $name = 'http:install';

    protected string $description = 'Install eazy http server.';

    protected string $help = 'This command allow you to create models...';

    protected array $argument = [
    ];
    

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Hello World!, %s', $input->getArgument('name')));
        return 0;
    }
}