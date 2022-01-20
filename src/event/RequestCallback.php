<?php


namespace eazy\http\event;


use Co\Context;
use DI\Container;
use eazy\di\Di;
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

class RequestCallback
{
    public function __invoke(Request $request, Response $response)
    {
        $app = App::$locator;
        try {
            $app->response->setResponse($response);
            $handler = $app->request->resolve($request);
            $app->controller->runAction($handler)->send();
        }catch (\Swoole\ExitException $exception){
            App::$locator->errorHandler->content = $exception->getStatus();
        }catch (\Throwable $exception) {
            App::$locator->errorHandler->handleException($exception);
        } finally {
            App::$locator->response->send();
        }

        unset(App::$attributes[Coroutine::getuid()]);
//        echo __FUNCTION__;
    }
}