<?php


namespace eazy\http\event;


use DI\Container;
use eazy\Eazy;
use eazy\http\Application;
use eazy\http\Components;
use eazy\http\RouterDispatcher;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;

class RequestCallback
{

    public static function onRequest(Request $request, Response $response)
    {
        var_dump(Eazy::$container->get('urlManager'));
        return $response->end('22');
        new Application($Request, $response);
        (new Eazy($request, $response))->run();
    }
}