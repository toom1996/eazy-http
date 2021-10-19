<?php
namespace eazy\http\event;


use DI\ContainerBuilder;
use eazy\di\Di;
use eazy\Eazy;
use eazy\http\App;
use eazy\http\Bootstrap;
use eazy\http\components\ErrorHandler;
use eazy\http\components\Request;
use eazy\http\components\Response;
use eazy\http\components\UrlManager;
use eazy\http\components\View;
use eazy\http\exceptions\InvalidConfigException;
use toom1996\base\Exception;
use toom1996\base\Stdout;
use toom1996\di\Container;
use toom1996\helpers\ConsoleHelper;
use toom1996\Launcher;
use toom1996\log\LogDispatcher;

/**
 *
 */
spl_autoload_register(['eazy\Eazy', 'autoload'], true, true);

class WorkerStartCallback
{

    private static $_config;

    const CORE_COMPONENTS = [
        'request' => ['class' => Request::class],
        'response' => ['class' => Response::class],
        'errorHandler' => ['class' => ErrorHandler::class],
        'urlManager' => ['class' => UrlManager::class],
        'view' => ['class' => View::class],
//        'assetManager' => ['class' => AssetManager::class],
//        'log' => ['class' => LogDispatcher::class],
    ];

    const BOOTSTRAP_COMPONENTS = [
        'urlManager',
    ];

    public static function onWorkerStart($server, int $workerId)
    {
        Eazy::$container = new Di();
        Eazy::setAlias('@eazy', dirname(__DIR__));
        try {
            $config = include APP_CONFIG;
            if (!isset($config[Bootstrap::$packageName]['config'])) {
                throw new InvalidConfigException("Unable to determine the eazy-http config.");
            }
            self::$_config = $config[Bootstrap::$packageName]['config'];

            self::initConfigure();
            // bootstrap components.
            self::bootstrapComponet();

            swoole_set_process_name($server->taskworker ? "TaskWorker#{$workerId}" :"Worker#{$workerId}");
        }catch (\Throwable $exception) {
            var_dump($exception);
            Eazy::error($exception->getMessage());
            exit($exception->getCode());
        }
    }


    public static function initConfigure()
    {
        $config = [];
        // Set aliases.
        if (isset(self::$_config['aliases']) && is_array(self::$_config['aliases'])) {
            foreach (self::$_config['aliases'] as $alias => $path) {
                Eazy::setAlias($alias, $path);
            }
        }

        foreach (self::CORE_COMPONENTS as $id => $component) {
            if (in_array($id, self::BOOTSTRAP_COMPONENTS)) {
                continue;
            }

            if (!isset(self::$_config['components'][$id])) {
                $config['components'][$id] = $component;
            }

            if (!isset(self::$config['components'][$id]['class'])) {
                $config['components'][$id]['class'] = $component['class'];
            }
        }

        App::$config = $config;
    }


    private static function bootstrapComponet()
    {
        if (!isset(self::$_config['bootstrap'])) {
            self::$_config['bootstrap'] = [];
        }

        $bootstrap = array_unique(array_merge(self::BOOTSTRAP_COMPONENTS, self::$_config['bootstrap']));
        foreach ($bootstrap as $component) {
            if (!isset(self::$_config['components'][$component])) {
                throw new InvalidConfigException("Invalid component id:{$component}");
            }
            Eazy::$container->set($component, self::$_config['components'][$component]);
        }
    }
}