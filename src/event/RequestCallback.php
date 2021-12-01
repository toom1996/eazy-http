<?php


namespace eazy\http\event;


use Co\Context;
use DI\Container;
use eazy\di\Di;
use eazy\http\App;
use eazy\http\Components;
use eazy\http\components\RequestHandler;
use eazy\http\Eazy;
use eazy\http\Module;
use eazy\http\Router;
use eazy\http\RouterDispatcher;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;

class RequestCallback extends Module
{
    public function __invoke(Request $request, Response $response)
    {
        try {
            $this->response->setResponse($response);
            $handler = $this->request->resolve($request);
            $result = $this->controller->runAction($handler);
            if ($result) {
                $this->response->content = $result;
            }
            $this->response->send();
        }catch (\Swoole\ExitException $exception){
            $this->response->content = $exception->getStatus();
        }catch (\Throwable $exception) {
            $this->errorHandler->handleException($exception);
        } finally {
            $this->response->send();
        }

        unset(Eazy::$attributes[Coroutine::getuid()]);
//        echo __FUNCTION__;
    }
}