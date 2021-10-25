<?php

namespace eazy\http\components;

use eazy\base\BaseObject;
use eazy\http\App;

class Request extends BaseObject
{
    /**
     * Request path
     * @var
     */
    private ?string $_pathInfo = null;

    /**
     * Query Params
     * @var
     */
    private $_queryParams;

    /**
     * Swoole fd
     * @var
     */
    public $fd;

    /**
     * Swoole stream id
     * @var
     */
    public $streamId;

    /**
     * Swoole request Header
     * @var
     */
    public $header;

    /**
     * Swoole request server
     * @var
     */
    public $server;

    /**
     * Swoole request cookie
     * @var
     */
    public $cookie;

    /**
     * Swoole request get.
     * @var
     */
    public $get;

    /**
     * Swoole request files
     * @var
     */
    public $files;

    /**
     * Swoole request post.
     * @var
     */
    public $post;

    /**
     * Swoole request temp files
     * @var
     */
    public $tmpfiles;

    /**
     * Request method.
     * @var
     */
    private $_method;

    /**
     * Resolove current request.
     * @return array
     * @throws \eazy\http\exceptions\NotFoundHttpException
     */
    public function resolve()
    {
        [$handler, $param] = App::$get->getUrlManager()->parseRequest();
        $this->setQueryParams($param + $this->getQueryParams());
        return [$handler, $this->getQueryParams()];
    }


    /**
     * Returns the path info of the currently requested URL.
     * @return mixed
     */
    public function getPathInfo()
    {
        return $this->server['path_info'] ?? '';
    }

    /**
     * Resolves the request URI portion for the currently requested URL.
     * @return mixed|string|string[]|null
     */
    public function getUrl()
    {
        $requestUri = $this->server['request_uri'];
        if ($requestUri !== '' && $requestUri[0] !== '/') {
            $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
        }
        return $requestUri;
    }

    /**
     * Return all query params.
     * @return array
     */
    private function getQueryParams(): array
    {
        return $this->get ?? [];
    }

    /**
     * Returns the request method given in the [[method]].
     *
     * Thid method will return the conentes of swoole `server['request_method']` if params where not explicitly set.
     * @return mixed
     */
    public function getMethod()
    {
        return $this->server['request_method'];
    }

    /**
     * Merge uri parameter and `GET` parameter and returns  parameter with a given name. If name isn't specified, returns all parameters.
     * @param string $name the parameter name
     * @param mixed $defaultValue the default parameter value if the parameter does not exist.
     * @return array|mixed
     */
    public function get($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->getQueryParams();
        }

        return $this->getQueryParam($name, $defaultValue);
    }

    /**
     * Returns the named GET parameter value.
     * If the GET parameter does not exist, the second parameter passed to this method will be returned.
     * @param string $name the GET parameter name.
     * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
     * @return mixed the GET parameter value
     * @see getBodyParam()
     */
    private function getQueryParam($name, $defaultValue = null)
    {
        $params = $this->getQueryParams();

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }


    /**
     * Sets the request [[queryString]] parameters.
     * @param array $values the request query parameters (name-value pairs)
     * @see getQueryParam()
     * @see getQueryParams()
     */
    private function setQueryParams($values)
    {
        $this->get = $values;
    }
}