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
            $this->request->initRequest($request);
            $this->response->initResponse($response);
            $handler = $this->request->resolve();
            $result = $this->controller->runAction($handler);
            if ($result) {
                $this->response->context->content = $result;
            }
            $this->response->send();
        }catch (\Swoole\ExitException $exception){
            var_dump($exception);
            $this->response->content = $exception->getStatus();
        }catch (\Throwable $exception) {
            var_dump($exception);
            $this->errorHandler->handleException($exception);
        } finally {
            var_dump($this->response->getContent());
            $this->response->send();
        }
        
        echo __FUNCTION__;
    }

}