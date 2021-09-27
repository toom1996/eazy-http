<?php

namespace eazy\http;

use eazy\base\BootstrapCommandInterface;
use eazy\http\command\HttpInstallCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Bootstrap implements BootstrapCommandInterface
{
    /**
     * @var array|string[] 
     */
    protected array $commands = [
        HttpInstallCommand::class
    ];

    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application &$console)
    {
        foreach ($this->commands as $command) {
            $console->add(new $command);
        }
    }
}