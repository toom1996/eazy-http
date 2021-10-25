<?php

namespace eazy\http;

use eazy\Eazy;
use Swoole\Http\Request;
use Symfony\Component\Console\Tester\TesterTrait;

/**
 * Class App
 * @property  \eazy\http\components\Request $request
 * @property  \eazy\http\components\Response $response
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
        $this->set('request', $request);
        self::$get = &$this;
    }

    /**
     * Run application.
     * @throws \ReflectionException
     * @throws \eazy\http\exceptions\InvalidConfigException
     * @throws \eazy\http\exceptions\UnknownClassException
     */
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
        $this->getLog()->flush();
        self::$get = null;
    }

    /**
     * Handle request.
     * @param $request \eazy\http\components\Request
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

    /**
     * Get request component.
     * @return \eazy\http\components\Request
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * Get log component.
     * @return \eazy\http\log\LogDispatcher
     */
    public function getLog()
    {
        return $this->get('log');
    }

    /**
     * Get urlManager component.
     * @return \eazy\http\components\UrlManager
     */
    public function getUrlManager()
    {
        return $this->get('urlManager');
    }

    /**
     * Get response component.
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
     * Get view component.
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
     * Get errorHandler component.
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
     * Quickly get the log component method.
     * Same as `getLog` method.
     * @return \eazy\http\log\LogDispatcher
     */
    public static function log()
    {
        return self::$get->getLog();
    }
}