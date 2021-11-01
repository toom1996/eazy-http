<?php

namespace eazy\http\server;


use eazy\http\Server;

class HttpServer extends Server
{
    /**
     * @var \Swoole\Http\Server|null
     */
    protected ?\Swoole\Http\Server $server;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->server = new \Swoole\Http\Server($this->host, $this->port, \server('mode'), SWOOLE_SOCK_TCP);
        $this->server->set($this->settings);
        $this->server->configPath = $this->configPath;
        foreach ($this->callbacks as $event => $callback) {
            $this->server->on($event, $callback);
        }
    }
}