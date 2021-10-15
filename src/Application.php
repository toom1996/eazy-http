<?php

namespace eazy\http;

class Application extends Module
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
    }

    /**
     * Initializes and executes bootstrap components.
     */
    public function bootstrap()
    {
        self::$app = &$this;
    }

    public function run()
    {
        var_dump($this->getComponet('request'));
//        try {
//            $this->handleRequest($this->getComponet('request'))
//                ->send();
//        }catch (\Swoole\ExitException $e){
//            $this->getResponse()->content = $e->getStatus();
//        }catch (\Throwable $e) {
//            $this->getErrorHandler()->handleException($e);
//        } finally {
//            $this->getResponse()->send();
//        }
//        $this->getLog()->flush();
//        self::$app = null;
    }

    public function handleRequest($request)
    {
        [$handler, $params] = $request->resolve();
        $result = $this->runAction($handler);
        $response = $this->getResponse();
        if ($result !== null) {
            $response->content = $result;
        }

        return $response;
    }
}