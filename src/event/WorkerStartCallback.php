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
use eazy\http\di\Container;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;
use eazy\http\helpers\FileHelper;
use eazy\http\Log;
use eazy\http\log\LogDispatcher;
use eazy\http\ServiceLocator;

spl_autoload_register(['eazy\http\App','autoload'], true, true);
class WorkerStartCallback
{
    private static $_config;

    const CORE_COMPONENTS = [
        'request' => ['class' => Request::class],
        'response' => ['class' => Response::class],
        'errorHandler' => ['class' => ErrorHandler::class],
        'urlManager' => ['class' => UrlManager::class],
        'view' => ['class' => View::class],
        'assetManager' => ['class' => AssetManager::class],
        'log' => ['class' => LogDispatcher::class],
    ];

    const BOOTSTRAP_COMPONENTS = [
//        'request' => \eazy\http\Request::class
    ];

    public static function onWorkerStart($server, int $workerId)
    {
        new Container();
        App::$component = new ServiceLocator();

        // bootstrap global component.
//        Container::$instance->set('request', [
//            'class' => \eazy\http\Request::class
//        ]);
//        Eazy::$container = new Di();
//        Eazy::$container->set('request', [
//            'class' => \eazy\http\Request::class
//        ]);
//        Eazy::$container->set('response', [
//            'class' => \eazy\http\Response::class
//        ]);
//
//        Eazy::$container->set('router', [
//            'class' => \eazy\http\Router::class
//        ]);
//        Container::$instance->set();
        
        App::setAlias('@eazy', dirname(__DIR__));
        try {
            self::bootstrap($server->configPath);
            var_dump(Container::$instance);
//            if (!isset($config[Bootstrap::$packageName])) {
//                throw new InvalidConfigException("Unable to determine the eazy-http config.");
//            }
//            self::$_config = $config[Bootstrap::$packageName];
//            self::initConfigure();
            // bootstrap components.
//            self::bootstrapComponet();

            swoole_set_process_name($server->taskworker ? "TaskWorker#{$workerId}" :"Worker#{$workerId}");
        }catch (\Throwable $exception) {
            var_dump($exception->getTraceAsString());
            var_dump($exception->getLine());
            Eazy::error($exception->getMessage());
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
        $config = [];
        if (is_dir($configPath)) {
            foreach (FileHelper::findFiles($configPath, ['only' => ['*.php']]) as $name => $file) {
                $config['component'][basename($file, '.php')] = require $file;
            }
        }else{
            $config = require $configPath;
        }

        // bootstrap component.
        foreach ($config['component'] as $componentName => $attributes) {
            if (isset($attributes['bootstrap']) && $attributes['bootstrap'] === true) {
                Container::$instance->set($componentName, $attributes);
            }
        }

        // set aliases.
        App::setAlias('@controllers', APP_PATH . '/controllers');
        App::setAlias('@app', APP_PATH);
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