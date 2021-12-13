<?php

namespace eazy\http;

use DI\Test\PerformanceTest\Get\C;
use eazy\di\Di;
use eazy\helpers\BaseArrayHelper;
use eazy\http\base\BaseComponent;
use eazy\http\di\Container;
use eazy\http\helpers\ArrayHelper;
use Swoole\Coroutine;

/**
 * @property integer $fd
 * @property integer $streamId
 * @property array $header
 * @property array $server
 *
 * @property string $requestTimeFloat the swoole server's request_time_float.
 * @property string $serverProtocol the swoole server's server_protocol.
 * @property string $requestTime the swoole server's request_time.
 * @property string $queryString the swoole server's query_string.
 * @property string $masterTime the swoole server's master_time.
 * @property string $remoteAddr the swoole server's remote_addr.
 * @property string $remotePort the swoole server's remote_port.
 * @property string $serverPort the swoole server's server_port.
 * @property string $pathInfo the swoole server's path_info.
 * @property string $method the swoole server's method.
 * @property string $uri the swoole server's request_uri.
 */
class Request extends ContextComponent
{
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

    /**
     * Current request info.
     * @return \Swoole\Http\Request
     */
    private function _request(): \Swoole\Http\Request
    {
        return $this->_request;
    }

    /**
     * Resolves the current request into a route and the associated parameters.
     * @param  \Swoole\Http\Request  $request the request info.
     * @return mixed
     * @throws \eazy\http\exceptions\NotFoundHttpException
     */
    public function resolve(\Swoole\Http\Request $request)
    {
        var_dump($request);
        $this->_request = $request;
        [$handler, $param] = Eazy::$component->router->parseRequest();
        $this->_routeParam = $param ?: [];
        return $handler;
    }

    /**
     * Returns request remote address.
     * @return mixed
     */
    public function getRemoteAddr()
    {
        return $this->_request()->server['remote_addr'];
    }

    /**
     * Returns request http method.
     * @return mixed
     */
    public function getMethod()
    {
        return $this->_request()->server['request_method'];
    }

    /**
     * Returns request uri.
     * @return mixed
     */
    public function getUri()
    {
        return $this->_request()->server['request_uri'];
    }

    /**
     * Returns GET parameter with a given name. If name isn't specified, returns an array of all GET parameters.
     * @param  null  $name the parameter name.
     * @param  null  $default the default value when parameter name does not exist.
     * @return array|mixed|null
     */
    public function get($name = null, $default = null)
    {
        $params = ArrayHelper::merge($this->_routeParam ,$this->getQueryParams());
        if ($name === null) {
            return $params;
        }

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    /**
     * Returns swoole GET parameters.
     * @return array
     */
    private function getQueryParams()
    {
        return $this->_request()->get;
    }

    /**
     * Returns swoole server's query string.
     * @return mixed
     */
    public function getQueryString()
    {
        return $this->_request()->server['query_string'] ?? null;
    }
}