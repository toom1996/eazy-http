<?php

namespace eazy\http;

use eazy\base\BootstrapCommandInterface;
use eazy\http\command\HttpStartCommand;
use eazy\http\command\HttpStopCommand;
use eazy\http\command\InstallCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Bootstrap implements BootstrapCommandInterface
{
    public static string $packageName = 'eazysoft/eazy-http';

    /**
     * @var array|string[] 
     */
    protected array $commands = [
        InstallCommand::class,
        HttpStartCommand::class,
        HttpStopCommand::class,
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