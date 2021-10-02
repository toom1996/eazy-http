<?php


namespace eazy\http\event;


use eazy\Eazy;
use toom1996\base\Exception;
use toom1996\base\Stdout;
use toom1996\di\Container;
use toom1996\helpers\ConsoleHelper;
use toom1996\Launcher;
use toom1996\log\LogDispatcher;

class WorkerErrorCallback
{
    public static function onWorkerError(Swoole\Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal)
    {
        echo '----------@@@@@@@@@@';
    }
}