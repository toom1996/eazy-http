<?php

namespace eazy\http;

use eazy\base\BaseObject;
use eazy\Eazy;
use eazy\http\event\RequestCallback;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use eazy\http\event\WorkerErrorCallback;
use eazy\http\event\WorkerStartCallback;
use Swoole\http\Server as swooleServer;

class Server extends BaseObject
{
    /**
     * Server host.
     * @var string
     */
    public ?string $host;

    /**
     * Server port.
     * @var int
     */
    public ?int $port;

    /**
     * Server setting.
     * @var array
     */
    public array $settings = [];

    /**
     * Server event callbacks.
     * @var array
     */
    public array $callbacks = [];

    /**
     * Initialize server.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Run swoole http server.
     */
    public function run(string $name)
    {
        $this->server->name = $name;
        $this->server->start();
    }
}