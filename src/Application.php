<?php

namespace eazy\http;

class Application
{
    /**
     * @var \eazy\http\Application
     */
    public static $app;

    /**
     * Eazy instance config.
     * @var array
     */
    public static $config;

    /**
     * @param  \Swoole\Http\Request  $request
     * @param  \Swoole\Http\Response  $response
     */
    public function __construct(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $this->bootstrap();
        $this->getResponse($response);
        $this->getRequest($request);
    }

    /**
     * Initializes and executes bootstrap components.
     */
    public function bootstrap()
    {
        self::$app = &$this;
    }
    
    public function getResponse()
    {
        
    }
    
    public function getRequest()
    {
        
    }
}