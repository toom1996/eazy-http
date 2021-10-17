<?php

namespace eazy\http\components;

use eazy\base\BaseObject;

class Request extends BaseObject
{
    /**
     * Request path
     * @var
     */
    private $_pathInfo;

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
     * Swoole request $_GET
     * @var
     */
    public $get;

    /**
     * Swoole request files
     * @var
     */
    public $files;

    /**
     * Swoole request $_POST
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

    public function getUrl()
    {
        $requestUri = $this->server['request_uri'];
        if ($requestUri !== '' && $requestUri[0] !== '/') {
            $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
        }
        return $requestUri;
    }
}