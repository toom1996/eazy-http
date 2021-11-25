<?php

namespace eazy\http;

use eazy\Eazy;
use eazy\helpers\BaseFileHelper;
use eazy\http\base\BaseComponent;
use eazy\http\di\Container;
use eazy\http\exceptions\NotFoundHttpException;
use eazy\http\exceptions\UnknownClassException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use http\Exception\BadMethodCallException;
use function FastRoute\simpleDispatcher;

#[\Attribute] class Router extends Component
{
    private $_adapter;

    public $route;
    
    public function init()
    {
        $this->_adapter = $this->getAdapter();
        parent::init(); // TODO: Change the autogenerated stub
    }

    private static $controllerMap;

    public function getAdapter()
    {
        $webRoute = $this->route;
        return simpleDispatcher(function (RouteCollector $controller) use ($webRoute) {
            var_dump('---------------------');
            foreach ($webRoute as $prefix => $rules) {
                echo '~~';
                if (count($rules) == count($rules, COUNT_RECURSIVE)) {
                    [$method, $route, $handler] = $this->parseRule($rules);
                    $controller->addRoute($method, $route, $handler);
                } else {
                    if (is_int($prefix)) {
                        [$method, $route, $handler] = $this->parseRule($rules);
                        $controller->addRoute($method, $route, $handler);
                    }else{
                        $controller->addGroup($prefix, function (RouteCollector $controller) use ($rules) {
                            foreach ($rules as $rulesChild) {
                                [$method, $route, $handler] = $this->parseRule($rulesChild);
                                $controller->addRoute($method, $route, $handler);
                            }
                        });
                    }
                }
            }
        });
    }

    private function parseRule($rule)
    {
        [$method, $route, $handler] = [$rule[0], $rule[1], $rule[2]];
        if (strpos($handler, '@') !== 0) {
            $handler = '@controllers' . $handler;
        }
//        $this->setControllerMap($handler);

        return [$method, $route, $handler];
    }

    public function parseRequest(): array
    {
        $match = $this->matchRoute();
        switch ($match[0]) {
            case Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException("Page Not Found.");
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new BadMethodCallException("Method Not Allowed.");
                break;
        }
        // handler, param
        return [$match[1], $match[2]];
    }
    
    public function matchRoute()
    {
        $request = Container::$instance->get('request');
        $httpMethod = $request->getMethod();
        $uri = $request->getUri();
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        return $this->_adapter->dispatch($httpMethod, $uri);
    }

    public static function runAction($path)
    {
        // If is register
        if (isset(self::$controllerMap[$path])) {
            $controller = self::$controllerMap[$path];
        }else{
            $handler = $path;
            // If route is `@controllers/site/index`, will be convert @controller to BathPath
            $handlerAlias = Eazy::getAlias($handler);
            $ex = explode('/', $handlerAlias);

            // Find controller and action.
            [$controller, $action] = array_slice($ex, -2, 2);

            if (strpos($controller, 'Controller') === false) {
                $controller = ucfirst($controller).'Controller';
            }

            if (strpos($action, 'action') === false) {
                $action = 'action'.ucfirst($action);
            }

            $handlerFile = implode('/',
                array_merge(array_slice($ex, 0, count($ex) - 2),
                    [$controller . '.php']));

            if (!file_exists($handlerFile)) {
                throw new UnknownClassException("{Unknown class {$handler}");
            }

            $classNamespace = BaseFileHelper::getNamespace($handlerFile);
            $className = '\\' . $classNamespace . '\\' . basename(str_replace('.php', '', $handlerFile));

            //        $ref = new \ReflectionClass($className);
            //        if (!$ref->hasMethod($action)) {
            //            throw new InvalidConfigException("class {$className} does not have a method {$action}, please check your config.");
            //        }
            self::$controllerMap[$handler] = [
                'class' => $className,
                'action' => $action
            ];
            $controller = self::$controllerMap[$handler];
        }
        
        $controller = Eazy::createObject($controller);
        if (is_object($controller) && $controller instanceof Controller) {
            return call_user_func([$controller, $controller->action]);
        }

        throw new InvalidConfigException("Unknown action.");
    }
}