<?php

namespace eazy\http;

use DI\Test\PerformanceTest\Get\C;
use eazy\di\Di;
use eazy\Eazy;
use eazy\helpers\BaseArrayHelper;
use eazy\http\base\BaseComponent;
use eazy\http\di\Container;

/**
 * @property string $method
 */
class Request extends BaseComponent
{
    public function fd()
    {
        return $this->context->fd;
    }

    public function streamId()
    {
        return $this->context->streamId;
    }

    public function header()
    {
        return $this->context->header;
    }
    
    public function server()
    {
        return $this->context->server;
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
        return $this->context->server['query_string'] ?? $default;
    }

    private function getQueryParams()
    {
        return $this->context->get ?? [];
    }

    private function getQueryParam($name, $defaultValue = null)
    {
        $params = $this->getQueryParams();

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    public function getMethod()
    {
        return $this->context->server['request_method'];
    }
    
    public function getUri()
    {
        return $this->context->server['request_uri'];
    }

    
    public function resolve()
    {
        Container::$instance->get(Router::class);
        [$handler, $param] = Eazy::$container->get('router')->parseRequest();
        return [$handler, $param];
    }
    
    public function setRequest(\Swoole\Http\Request $request)
    {
        Context::put($this->getObjectId(), $request);
        return $this;
    }
}