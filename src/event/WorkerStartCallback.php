<?php
namespace eazy\http\event;


use app\api\v1\aspects\TestAspect;
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
use eazy\http\Event;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;
use eazy\http\helpers\ArrayHelper;
use eazy\http\helpers\FileHelper;
use eazy\http\helpers\StringHelper;
use eazy\http\Hook;
use eazy\http\Log;
use eazy\http\log\LogDispatcher;
use eazy\http\Scanner;
use eazy\http\ServiceLocator;
use Swoole\Coroutine;


spl_autoload_register(['eazy\http\App','autoload'], true, true);

/**
 * 
 */
class WorkerStartCallback
{
    private static $_config;

    // Http core component.
    const CORE_COMPONENTS = [
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
            App::info('Server start.');
            new App();
            self::bootstrap($server->configPath);
            swoole_set_process_name($server->taskworker ? "TaskWorker#{$workerId}" :"Worker#{$workerId}");
        }catch (\Throwable $exception) {
            App::error([
                'type' => get_class($exception),
                'file' => method_exists($exception, 'getFile') ? $exception->getFile() : '',
                'errorMessage' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'stack-trace' => explode("\n", $exception->getTraceAsString()),
            ]);
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

        // init aliase
        foreach ($config['aliases'] as $name => $path) {
            App::setAlias($name, $path);
        }

        // init component
        $config['components'] = ArrayHelper::merge(self::CORE_COMPONENTS, $config['components']);
        foreach ($config['components'] as $componentName => $attributes) {
            $class = App::createObject($attributes);
            if ($class instanceof ContextComponent) {
                App::$locator->set($componentName, $class);
            }
        }

        // init hook
        foreach ($config['hook'] as $hook) {
            App::createObject($hook);
        }
    }
}