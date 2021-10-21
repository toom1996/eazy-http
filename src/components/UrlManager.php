<?php

namespace eazy\http\components;

use eazy\Eazy;
use eazy\helpers\BaseFileHelper;
use eazy\http\App;
use eazy\http\Component;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\NotFoundHttpException;
use eazy\http\exceptions\UnknownClassException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * @property array $controllerMap
 */
class UrlManager extends Component
{
    public $route = [];

    /**
     * @var \FastRoute\Dispatcher
     */
    private $_adapter;

    private $_controllerMap;

    public function init()
    {
        $this->_adapter = $this->getAdapter();
        parent::init(); // TODO: Change the autogenerated stub
    }


    public function parseRequest(): array
    {
        $match = $this->matchRoute();
        switch ($match[0]) {
            case Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException("Page Not Found.");
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedHttpException("Method Not Allowed.");
                break;
        }
        // handler, param
        return [$match[1], $match[2]];
    }

    protected function matchRoute()
    {
        $request = App::$get->getRequest();
        $httpMethod = $request->getMethod();
        $uri = $request->getUrl();
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        return $this->_adapter->dispatch($httpMethod, $uri);
    }

    private function trimSlashes($url): string
    {
        if ($url !== '/') {
            $url = rtrim($url, '/');
        }

        $url = preg_replace('#/+#', '/', $url);
        if ($url === '') {
            return '/';
        }

        return $url;
    }

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

    /**
     *
     * @param $rule
     *
     * @return array
     * @throws \ReflectionException
     */
    private function parseRule($rule)
    {
        [$method, $route, $handler] = [$rule[0], $rule[1], $rule[2]];
        if (strpos($handler, '@') !== 0) {
            $handler = '@controllers' . $handler;
        }
        $this->setControllerMap($handler);

        return [$method, $route, $handler];
    }

    /**
     * Set to controller map.
     * @param $handler
     *
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     * @throws \toom1996\base\UnknownClassException
     */
    public function setControllerMap($handler)
    {

        if (isset($this->_controllerMap[$handler])) {
            var_dump($this->_controllerMap);
            return $this->_controllerMap[$handler];
        }

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
        $this->_controllerMap[$handler] = [
            'class' => $className,
            'action' => $action
        ];
        return $this->_controllerMap[$handler];
    }

    /**
     * Return controller map.
     * @return array
     */
    public function getControllerMap()
    {
        return $this->_controllerMap;
    }

}