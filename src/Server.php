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
    const HTTP_EVENT = [
        SwooleEvent::SWOOLE_ON_START => [StartCallback::class, 'onStart'],
        SwooleEvent::SWOOLE_ON_REQUEST => [RequestCallback::class, 'onRequest'],
        SwooleEvent::SWOOLE_ON_WORKER_START => [WorkerStartCallback::class, 'onWorkerStart'],
        SwooleEvent::SWOOLE_ON_WORKER_ERROR => [WorkerErrorCallback::class, 'onWorkerError'],
    ];

    /**
     * Server host.
     * @var string
     */
    public string $host;

    /**
     * Server port.
     * @var int
     */
    public int $port;

    /**
     * Server setting.
     * @var array
     */
    public array $setting = [];

    /**
     * Server event.
     * @var array
     */
    public array $event = [];

    public function init()
    {
        $this->server = new swooleServer($this->host, $this->port);
        $this->server->set($this->setting);
        $this->event = array_merge($this->event, self::HTTP_EVENT);
        foreach ($this->event as $event => $callback) {
            $this->server->on($event, $callback);
        }
        parent::init();
    }

    /**
     * Run swoole http server.
     */
    public function run()
    {
        $this->server->start();
    }
}