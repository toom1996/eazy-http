<?php

namespace eazy\http;

use eazy\Eazy;
use Swoole\Http\Request;
use Symfony\Component\Console\Tester\TesterTrait;

/**
 * Class App
 * @property  \eazy\http\components\Request $request
 * @property  \eazy\http\components\Response $response
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
//        $response->detach();
        $this->request = $request;
//        $this->response = $response;
        self::$get = &$this;
    }

    public function __destruct()
    {
//        echo 'd';
        // TODO: Implement __destruct() method.
    }

    public function run()
    {
        try {
            $this->handleRequest($this->getRequest())
                ->send();
        }catch (\Swoole\ExitException $e){
            $this->getResponse()->content = $e->getStatus();
        }catch (\Throwable $e) {
            $this->getErrorHandler()->handleException($e);
        } finally {
            $this->getResponse()->send();
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
     * @return \eazy\http\components\Response
     */
    public function getResponse()
    {
        if (!$this->has('response')) {
            $this->set('response');
        }

        return $this->get('response');
    }

    /**
     * @return \eazy\http\components\View
     */
    public function getView()
    {
        if (!$this->has('view')) {
            $this->set('view');
        }

        return $this->get('view');
    }

    /**
     * @return \eazy\http\components\ErrorHandler
     */
    public function getErrorHandler()
    {
        if (!$this->has('errorHandler')) {
            $this->set('errorHandler');
        }

        return $this->get('errorHandler');
    }

    /**
     * @param $request \eazy\http\components\Request
     *
     * @return \eazy\http\components\Response
     */
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
    
    public function setRequest(\Swoole\Http\Request $request)
    {
        $this->set('request', $request);
    }

    public function setResponse(\Swoole\Http\Response $response)
    {
        $this->set('response', $response);
    }
}