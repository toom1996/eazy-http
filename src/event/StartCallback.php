<?php


namespace eazy\http\event;

use toom1996\base\BaseConsole;
use toom1996\base\Stdout;
use toom1996\helpers\ConsoleHelper;

class StartCallback
{
    public static function onStart(\Swoole\Server $server)
    {
//        Stdout::info('Eazy framework is running!');
        // https://wiki.swoole.com/#/functions?id=swoole_set_process_name
        swoole_set_process_name("Master {$server->master_pid} - {$server->port}");
    }
}