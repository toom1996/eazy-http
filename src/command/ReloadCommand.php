<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\Bootstrap;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use eazy\http\Server;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReloadCommand extends BaseCommand
{
    protected string $name = 'http:reload';

    protected string $description = 'Start eazy http server.';

    protected string $help = 'This command allow you to create models...';

    protected array $arguments = [
    ];

    public function execute(InputInterface $input, OutputInterface $output)
    {
         @posix_kill($pid, $Signal);
         return 0;
    }
}