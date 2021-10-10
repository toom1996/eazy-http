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
    protected string $name = 'http:start';

    protected string $description = 'Start eazy http server.';

    protected string $help = 'dddddddd';

    protected array $arguments = [
//        ['d', InputArgument::OPTIONAL, 'this is a optional argument']
    ];

    protected array $options = [
        ['daemonize', 'd', InputOption::VALUE_NONE, 'How many times should the message be printed?'],
//        ['config', null, InputOption::VALUE_OPTIONAL, 'How many times should the message be printed?'],
    ];

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $isDaemonize = $input->getOption('daemonize');
//        $config = $input->getOption('config');


        $config = require APP_PATH . '/app.php';
        $config = ($config[Bootstrap::$packageName]['server']);
        var_dump(count($config));
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