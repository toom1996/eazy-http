<?php


namespace eazy\http\event;


use eazy\Eazy;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;

class RequestCallback
{

    public static function onRequest(Request $request, Response $response)
    {
        return $response->end('22');
        (new Eazy($request, $response))->run();
    }
}