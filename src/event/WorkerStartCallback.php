<?php


namespace eazy\http\event;


use DI\ContainerBuilder;
use eazy\di\Di;
use eazy\Eazy;
use eazy\http\Bootstrap;
use eazy\http\components\UrlManager;
use toom1996\base\Exception;
use toom1996\base\Stdout;
use toom1996\di\Container;
use toom1996\helpers\ConsoleHelper;
use toom1996\Launcher;
use toom1996\log\LogDispatcher;

class WorkerStartCallback
{
    const CORE_COMPONENTS = [
        'request' => ['class' => Request::class],
        'response' => ['class' => Response::class],
        'errorHandler' => ['class' => ErrorHandler::class],
        'urlManager' => ['class' => UrlManager::class],
        'view' => ['class' => View::class],
        'assetManager' => ['class' => AssetManager::class],
        'log' => ['class' => LogDispatcher::class],
    ];

    public static function onWorkerStart($server, int $workerId)
    {
        Eazy::$container = new Di();
        try {
            $config = include APP_CONFIG;
            foreach ($config[Bootstrap::$packageName]['config']['bootstrap'] as $component) {
                Eazy::$container->set($component, $config[Bootstrap::$packageName]['config']['components'][$component]);
            }
            if ($server->taskworker) {
                $workerAlias = "TaskWorker#{$workerId}";
            } else {
                $workerAlias = "Worker#{$workerId}";
            }

            Eazy::info($workerAlias);
            swoole_set_process_name($workerAlias);
        }catch (\Throwable $exception) {
            Eazy::error($exception->getMessage());
            exit($exception->getCode());
        }

//        Eazy::$config = require APP_PATH.'/config/config.php';
        
        spl_autoload_register(function ($className) {
            echo '-----------------';
            if (strpos($className, '\\') !== false) {
                $classFile =
                    Eazy::getAlias('@'.str_replace('\\', '/', $className)
                        .'.php', false);
                if ($classFile === false || ! is_file($classFile)) {
                    return;
                }
            } else {
                return;
            }

            require $classFile;
        }, true, true);
//        self::initConfigure();
    }

    /**
     * Initialize configure.
     * Set aliases and merge core components.
     */
    public static function initConfigure()
    {
        // Set aliases.
        if (isset(Eazy::$config['aliases'])
            && is_array(Eazy::$config['aliases'])
        ) {
            foreach (Eazy::$config['aliases'] as $alias => $path) {
                Eazy::setAlias($alias, $path);
            }
        }

//        // merge core components with custom components.
//        foreach (self::CORE_COMPONENTS as $id => $component) {
//            if (!isset(Eazy::$config['components'][$id])) {
//                Eazy::$config['components'][$id] = $component;
//            }
//
//            if (!isset(Eazy::$config['components'][$id]['class'])) {
//                Eazy::$config['components'][$id]['class'] = $component['class'];
//            }
//        }
//
//        foreach (Eazy::$config['bootstrap'] as $component) {
//            $def = Eazy::$config['components'][$component];
//            Eazy::$config['components'][$component] = Eazy::createObject($def);
//        }
    }


    private function initBootstrapComponet($c)
    {

    }
}