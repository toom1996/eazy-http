<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use eazy\http\Server;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HttpStopCommand extends BaseCommand
{
    protected string $name = 'http:stop';

    protected string $description = 'Stop eazy http server.';

    protected string $help = 'This command allow you to create models...';

    protected array $arguments = [
    ];

    public function execute(InputInterface $input, OutputInterface $output)
    {
        
        return 0;
    }
}