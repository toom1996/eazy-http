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
    
    public static function onRequest(Request $request, Response $response)
    {
        \eazy\http\Context::put('request', $request);
        \eazy\http\Context::put('response', $response);
        try {
            [$handler, $params] = App::getRequest()->resolve();
            $result = Router::runAction($handler);
            if ($result !== null) {
                App::getResponse()->setContent($result);
            }

        }catch (\Swoole\ExitException $exception){

        }catch (\Throwable $exception) {
            var_dump($exception);
        } finally {

        }
        // BEFORE END
//        echo '@@';
        (App::getResponse()->send('hello' . $result));
                \eazy\http\Context::delete('request');
                \eazy\http\Context::delete('response');
//        $response->end(\eazy\http\Context::get('request')->server['query_string']);
//        echo 'unset';
//        (new App($request, $response))->run();
    }

    private function handleRequest()
    {

    }


    public function __invoke(Request $request, Response $response)
    {
        try {
            $handler = $this->request->setRequest($request)->resolve();
            $result = $this->controller->runAction($handler);
//            var_dump($result);
//            var_dump($handler);
//            var_dump($handler);
//            var_dump($params);
        }catch (\Swoole\ExitException $exception){
            var_dump($exception);
        }catch (\Throwable $exception) {
            var_dump($exception);
        } finally {

        }

        \eazy\http\Context::delete();
//        echo __FUNCTION__;
    }

    public function emit()
    {

    }
}