<?php


namespace eazy\http\event;

class SwooleEvent
{
    /**
     * Swoole event `onWorkerStart`.
     * For details, please see https://wiki.swoole.com/#/server/events?id=onworkerstart.
     */
    const SWOOLE_ON_WORKER_START = 'workerStart';

    /**
     * Swoole event `onWorkerError`.
     * For details, please see https://wiki.swoole.com/#/server/events?id=onworkererror.
     */
    const SWOOLE_ON_WORKER_ERROR = 'workerError';

    /**
     * Swoole event `onRequest`.
     * For details, please see https://wiki.swoole.com/#/websocket_server?id=onrequest.
     */
    const SWOOLE_ON_REQUEST = 'request';

    /**
     * Swoole event `onStart`.
     * For details, please see https://wiki.swoole.com/#/server/events?id=onstart.
     */
    const SWOOLE_ON_START = 'start';


}