<?php


namespace eazy\http\event;


use DI\Container;
use eazy\Eazy;
use eazy\http\App;
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
        $response->end(123);
//        (new App($request, $response))->run();
    }
}