<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\Bootstrap;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use eazy\http\Server;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
//        ['daemonize', 'd', InputOption::VALUE_NONE, 'Use daemonize mode ?'],
        ['server', 's', InputOption::VALUE_OPTIONAL, 'Start specified server.'],
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
        $isDaemonize = $input->getOption('server');
        var_dump($isDaemonize);
//        $isDaemonize = $input->getOption('daemonize');
//        $config = $input->getOption('config');


//        $config = require APP_CONFIG;
//        $config = ($config[Bootstrap::$packageName]['server']);
//        var_dump(count($config));
//        if (count($config) > 1) {
//
//        }
//        $config = ($config[Bootstrap::$packageName]['server']);
//        (new Server($config))->run();
//        var_dump($aa);
         $output->write("fuck you~");
         return 0;
    }
}