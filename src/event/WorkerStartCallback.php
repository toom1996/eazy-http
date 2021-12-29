<?php
namespace eazy\http\event;


use app\controllers\SiteController;
use Co\Client;
use Co\WaitGroup;
use DI\ContainerBuilder;
use eazy\di\Di;
use eazy\http\App;
use eazy\http\Attributes;
use eazy\http\Bootstrap;
use eazy\http\components\ErrorHandler;
use eazy\http\components\Request;
use eazy\http\components\Response;
use eazy\http\components\UrlManager;
use eazy\http\components\View;
use eazy\http\Connection;
use eazy\http\ContextComponent;
use eazy\http\databases\DbConnection;
use eazy\http\di\Container;
use eazy\http\Eazy;
use eazy\http\Event;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;
use eazy\http\helpers\ArrayHelper;
use eazy\http\helpers\FileHelper;
use eazy\http\Log;
use eazy\http\log\LogDispatcher;
use eazy\http\Scanner;
use eazy\http\ServiceLocator;
use Swoole\Coroutine;

/**
 * 
 */
spl_autoload_register(['eazy\http\Eazy','autoload'], true, true);

/**
 * 
 */
class WorkerStartCallback
{
    private static $_config;

    // Http core component.
    const CORE_COMPONENTS = [
        'scanner' => [
            'class' => Scanner::class,
        ],
        'controller' => ['class' => \eazy\http\Controller::class],
        'request' => ['class' => \eazy\http\Request::class],
        'response' => ['class' => \eazy\http\Response::class],
        'errorHandler' => ['class' => \eazy\http\components\ErrorHandler::class],
        'view' => ['class' => \eazy\http\web\View::class],
        'router' => ['class' => \eazy\http\Router::class],
        'db' => ['class' => DbConnection::class],
        'event' => ['class' => Event::class],
    ];

    public static function onWorkerStart($server, int $workerId)
    {
        try {
            new Container();
            Eazy::$component = new ServiceLocator();
            self::bootstrap($server->configPath);
            swoole_set_process_name($server->taskworker ? "TaskWorker#{$workerId}" :"Worker#{$workerId}");
        }catch (\Throwable $exception) {
            // TODO handle exception.
            var_dump($exception);
            var_dump($exception->getLine());
            var_dump($exception->getMessage());
            exit($exception->getCode());
        }
    }

    /**
     * Parse config.
     * @param $configPath
     *
     * @throws \eazy\http\exceptions\InvalidConfigException
     */
    private static function bootstrap($configPath)
    {
        $config = require $configPath;
        // set aliases.
        foreach ($config['aliases'] as $name => $path) {
            Eazy::setAlias($name, $path);
        }

        $config['components'] = ArrayHelper::merge(self::CORE_COMPONENTS, $config['components']);
        // bootstrap component.
        foreach ($config['components'] as $componentName => $attributes) {
            $class = Eazy::createObject($attributes);
            if ($class instanceof ContextComponent) {
                Container::$instance->set($componentName, $class);
            }
        }
    }
}