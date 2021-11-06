<?php


namespace eazy\http;


use app\controllers\SiteController;
use eazy\Eazy;
use eazy\helpers\BaseFileHelper;
use eazy\http\di\Container;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;

/**
 * @property string $actionId
 * @property string $action
 */
class Controller extends Component
{
    private static $_controllerMap;

    private array $_controllerMapParams = [];


    public function runAction($path)
    {
        $controller = $this->setControllerMap($path);
        if (is_object($controller)) {
            return call_user_func([$controller, $this->action]);
        }

        throw new InvalidConfigException("Unknown action.");
    }
    
    
    private function setControllerMap($handler)
    {
        $controllerMap = $params = [];
        if (isset(self::$_controllerMap[$handler])) {
            $params = $this->_controllerMapParams[$handler];
            $controllerMap = self::$_controllerMap[$handler];
        }else{
            // If route is `@controllers/site/index`, will be convert @controller to BathPath
            $handlerAlias = App::getAlias($handler);
            $ex = explode('/', $handlerAlias);

            // Find controller and action.
            [$controller, $action] = array_slice($ex, -2, 2);
            if (strpos($controller, 'Controller') === false) {
                $controller = ucfirst($controller).'Controller';
            }
            $actionId = $action;
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

            $this->_controllerMapParams[$handler] = [
                'action' => $action,
                'actionId' => $actionId,
            ];

            self::$_controllerMap[$handler] = App::createObject([
                'class' => $className
            ]);
        }
        $this->setControllerAttributes($this->_controllerMapParams[$handler]);

        return self::$_controllerMap[$handler];
    }

    private function setControllerAttributes(array $params)
    {
        foreach ($params as $paramName => $paramValue) {
            $this->setContext($paramName, $paramValue);
        }
    }


    public function getAction()
    {
        return $this->context->action ?? null;
    }

    public function getActionId()
    {
        return $this->context->actionId ?? null;
    }
}