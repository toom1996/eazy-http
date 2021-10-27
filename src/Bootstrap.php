<?php

namespace eazy\http;

use DI\ContainerBuilder;
use eazy\base\BootstrapCommandInterface;
use eazy\Eazy;
use eazy\http\command\ReloadCommand;
use eazy\http\command\StartCommand;
use eazy\http\command\StatusCommand;
use eazy\http\command\StopCommand;
use eazy\http\command\InstallCommand;
use Swoole\Http\Status;
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
        StartCommand::class,
        StopCommand::class,
        ReloadCommand::class,
        StatusCommand::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application &$console)
    {
        Log::$logger = '123';
        foreach ($this->commands as $command) {
            $console->add(new $command);
        }
    }
}