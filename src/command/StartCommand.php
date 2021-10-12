<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\Bootstrap;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use eazy\http\Server;
use Swoole\FastCGI;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Swoole\Process as SwooleProcess;

class StartCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected string $name = 'http:start';

    /**
     * {@inheritdoc}
     */
    protected string $description = 'Start eazy http server.';

    /**
     * {@inheritdoc}
     */
    protected array $options = [
        ['server', 's', InputOption::VALUE_REQUIRED, 'Which server want to start?'],
    ];


    /**
     * {@inheritdoc}
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $server = $input->getOption('server');
        if ($server === null) {
            $output->writeln("<error>Please select at least one server.</error>");
            return 0;
        }
        $config = require APP_CONFIG;
        $serverConfigs = ($config[Bootstrap::$packageName]['server']);
        $server = explode(',', $server);
        foreach ($serverConfigs as $serverConfig) {
            // daemonize mode
            $serverConfig['setting']['daemonize'] = true;
            if (is_array($server) && isset($serverConfig['name']) && in_array($serverConfig['name'], $server)) {
                $process = new SwooleProcess(function () use ($serverConfig) {
                    $server = new Server($serverConfig);
                    $server->run();
                });
                $process->start(); // 启动子进程
                $output->writeln("<info>Server#{$serverConfig['name']} start.</info>");
                SwooleProcess::wait();
            }
        }

        return 0;
    }
}