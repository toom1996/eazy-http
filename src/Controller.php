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
class Controller extends ContextComponent
{
    private static $_controllerMap;

    private array $_controllerMapParams = [];
    public function render($view, $params = [])
    {
        $content = App::$get->getView()->render($view, $params);
        return $this->renderContent($content);
    }


    /**
     * @param $content
     *
     * @return bool|string
     */
    public function renderContent($content)
    {
        $layoutFile = $this->findLayoutFile(App::$get->getView());
        if ($layoutFile !== false) {
            return App::$get->getView()->renderFile($layoutFile, ['content' => $content], $this);
        }

        return $content;
    }


    /**
     *
     *
     * @param $view View
     *
     * @return bool|string
     */
    public function findLayoutFile($view)
    {
        if (is_string($this->layout)) {
            $layout = $this->layout;
        }

        if (!isset($layout)) {
            return false;
        }

        if (strncmp($layout, '@', 1) === 0) {
            $file = Eazy::getAlias($layout);
        }
        //        elseif (strncmp($layout, '/', 1) === 0) {
        //            $file = YiiS::$app->getLayoutPath() . DIRECTORY_SEPARATOR . substr($layout, 1);
        //        } else {
        //            $file = $module->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
        //        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $view->defaultExtension;
        if ($view->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }


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
        $this->setControllerContext($this->_controllerMapParams[$handler]);

        return self::$_controllerMap[$handler];
    }

    private function setControllerContext($params)
    {
        Context::put($this->getObjectId(), [
            'action' => $params['action'],
            'actionId' => $params['actionId'],
        ]);
    }


    public function getAction()
    {
        return $this->getContext()['action'];
    }
}