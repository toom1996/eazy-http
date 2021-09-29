<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use eazy\http\Server;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HttpStartCommand extends BaseCommand
{
    protected string $name = 'http:start';

    protected string $description = 'Start eazy http server.';

    protected string $help = 'This command allow you to create models...';

    protected array $arguments = [
    ];

    public function execute(InputInterface $input, OutputInterface $output)
    {
        (new Server([
            'server' => \toom1996\server\http\Server::class,
            'host' => "0.0.0.0",
            'port' => 9503,
            'event' => [
                SwooleEvent::SWOOLE_ON_START => [StartCallback::class, 'onStart'],
            ],
            'setting' => [
                //        'enable_static_handler' => APP_DEBUG,
                //        'document_root' => APP_PATH . '/web',
                'worker_num' => 2,
                'enable_coroutine' => true,
                'hook_flags' => SWOOLE_HOOK_ALL,
                'daemonize' => false,
                'log_file' => APP_PATH . '/runtime/http.log',
                'pid_file' => APP_PATH . '/runtime/server.pid',
            ],
        ]))->run();
         $output->write("fuck you~");
         return 0;
    }
}