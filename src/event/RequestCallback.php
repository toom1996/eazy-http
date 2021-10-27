<?php


namespace eazy\http\event;


use Co\Context;
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
        \eazy\http\Context::put('request', $request);
//        \eazy\http\Context::delete('request');
        echo '@@';
        var_dump(Eazy::$container->get('request')->queryString());
        $response->end(\eazy\http\Context::get('request')->server['query_string']);
        echo 'unset';
//        (new App($request, $response))->run();
    }
}