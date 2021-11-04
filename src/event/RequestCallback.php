<?php


namespace eazy\http\event;


use Co\Context;
use DI\Container;
use eazy\di\Di;
use eazy\Eazy;
use eazy\http\App;
use eazy\http\Components;
use eazy\http\components\RequestHandler;
use eazy\http\Module;
use eazy\http\Router;
use eazy\http\RouterDispatcher;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;

class RequestCallback extends Module
{
    private function handleRequest()
    {

    }
    
    public function __invoke(Request $request, Response $response)
    {
        try {
            $this->initRequest();
            $this->request->context = $request;
            $this->response->context = $response;
            $handler = $this->request->resolve();
            $result = $this->controller->runAction($handler);
            if ($result) {
                $this->response->content = $result;
            }
            $this->response->send();
        }catch (\Swoole\ExitException $exception){
            $this->response->content = $exception->getStatus();
        }catch (\Throwable $exception) {
            var_dump($exception);
            $this->errorHandler->handleException($exception);
        } finally {
//            $this->response->send();
            \eazy\http\Context::delete();
        }
        
//        echo __FUNCTION__;
    }

    public function initRequest()
    {
        App::$pool[App::getUid()] = (Object)[];
    }
}