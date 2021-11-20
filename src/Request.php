<?php

namespace eazy\http;

use DI\Test\PerformanceTest\Get\C;
use eazy\di\Di;
use eazy\Eazy;
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
class Request extends Component
{
    
    public function getRequest()
    {
        return $this->attributes['request'];
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
        $this->setAttribute('request', $request);
        [$handler, $param] = App::$component->router->parseRequest();
        return $handler;
    }
}