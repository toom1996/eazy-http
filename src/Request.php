<?php

namespace eazy\http;

use DI\Test\PerformanceTest\Get\C;
use eazy\di\Di;
use eazy\helpers\BaseArrayHelper;
use eazy\http\base\BaseComponent;
use eazy\http\di\Container;
use Swoole\Coroutine;

/**
 * @property string $method
 * @property integer $fd
 * @property integer $streamId
 * @property array $header
 * @property array $server
 */
class Request extends ContextComponent
{
    
    public function getRequest()
    {
        return $this->properties['request'];
    }
    
    public function getFd()
    {
        return $this->request->fd;
    }

    public function getStreamId()
    {
        return $this->request->streamId;
    }

    public function getHeader(): array
    {
        return $this->request->header;
    }
    
    public function getServer()
    {
        return $this->request->server;
    }
    
    public function get($name = null, $default = null)
    {
        if ($name === null) {
            return $this->getQueryParams();
        }

        return $this->getQueryParam($name, $default);
    }

    public function queryString($default = null)
    {
        return $this->request->server['query_string'] ?? $default;
    }

    private function getQueryParams()
    {
        return $this->request->get ?? [];
    }

    private function getQueryParam($name, $defaultValue = null)
    {
        $params = $this->getQueryParams();

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    public function getMethod()
    {
        return $this->request->server['request_method'];
    }
    
    public function getUri()
    {
        return $this->request->server['request_uri'];
    }

    
    public function resolve(\Swoole\Http\Request $request)
    {
        $this->setProperty('request', $request);
        [$handler, $param] = Eazy::$component->router->parseRequest();

        var_dump('HANDLER') . PHP_EOL;
        var_dump($handler);
        var_dump($param);
        return $handler;
    }
}