<?php

namespace eazy\http;

use Swoole\Http\Request;
use Symfony\Component\Console\Tester\TesterTrait;

/**
 * Class App
 * @property-read \eazy\http\components\Request $request
 *
 * @author TOOM <1023150697@qq.com>
 * 
 */
class App extends Module
{
    /**
     * @var App
     */
    public static $get;

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
        $this->request = $request;
        self::$get = &$this;
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function run()
    {
//        var_dump($this->getComponet('urlManager'));
        try {
            $this->handleRequest($this->getRequest());
        }catch (\Swoole\ExitException $e){
            var_dump($e->getMessage());
//            $this->getResponse()->content = $e->getStatus();
        }catch (\Throwable $e) {
            var_dump($e->getMessage());
//            $this->getErrorHandler()->handleException($e);
        } finally {
//            $this->getResponse()->send();
        }
//        $this->getLog()->flush();
        self::$get = null;
    }

    public static function getComponent()
    {
        return self::$get;
    }

    /**
     * @return \eazy\http\components\Request
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * @return \eazy\http\components\UrlManager
     */
    public function getUrlManager()
    {
        return $this->get('urlManager');
    }

    /**
     *
     *
     * @param $request \eazy\http\components\Request
     */
    public function handleRequest($request)
    {
        [$handler, $params] = $request->resolve();
//        $result = $this->runAction($handler);
//        $response = $this->getResponse();
//        if ($result !== null) {
//            $response->content = $result;
//        }
//
//        return $response;
    }
    
    public function setRequest(Request $request)
    {
        $this->set('request', $request);
    }
}