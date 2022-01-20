<?php


namespace eazy\http;


use app\controllers\SiteController;
use eazy\console\StdoutLogger;
use eazy\helpers\BaseFileHelper;
use eazy\http\di\Container;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;
use Swoole\FastCGI\Record\Stdout;

/**
 * @property string $method
 * @property string $action
 * @property \eazy\http\components\View $view
 */
class Controller extends ContextComponent
{
    /**
     * Controller map.
     * @var array
     */
    private array $_controllerMap = [];

    private array $_controllerMapParams = [];


    const EVENT_BEFORE_ACTION = 'beforeAction';

    const EVENT_AFTER_ACTION = 'afterAction';

    /**
     * @param $path
     *
     * @return \eazy\http\Sender
     * @throws \eazy\http\exceptions\InvalidConfigException
     * @throws \eazy\http\exceptions\UnknownClassException
     */
    public function runAction($path)
    {
        $controller = $this->getController($path);
        if (is_object($controller) && $controller instanceof \eazy\http\web\Controller) {
            return $controller->run();
        }

        throw new InvalidConfigException("Run action fail.");
    }

    private function setControllerMap($handler)
    {
        if (!isset($this->_controllerMap[$handler])) {
            $handlerAlias = App::getAlias($handler);
            $params = explode('/', $handlerAlias);

            // find controller and action.
            [$controller, $action] = array_slice($params, -2, 2);
            if (strpos($controller, 'Controller') === false) {
                $controller = ucfirst($controller).'Controller';
            }
            $config['action'] = $action;
            if (strpos($action, 'action') === false) {
                $action = 'action'.ucfirst($action);
            }

            $handlerFile = implode('/',
                array_merge(array_slice($params, 0, count($params) - 2),
                    [$controller . '.php']));

            if (!file_exists($handlerFile)) {
                throw new UnknownClassException("{Unknown class {$handler}");
            }

            $config['method'] = $action;

            $classNamespace = BaseFileHelper::getNamespace($handlerFile);
            $className = '\\' . $classNamespace . '\\' . basename(str_replace('.php', '', $handlerFile));

            //            echo 'new Controller' . PHP_EOL;
            $this->_controllerMap[$handler] = App::createObject($className, $config);
        }

        var_dump($this->_controllerMap[$handler]);
        return $this->_controllerMap[$handler];
    }


    private function getController($handler)
    {
       if (!isset($this->_controllerMap[$handler])) {
           $mapping = [];
           $handlerAlias = App::getAlias($handler);
           $params = explode('/', $handlerAlias);

           [$controller, $action] = array_slice($params, -2, 2);
           if (strpos($controller, 'Controller') === false) {
               $controller = ucfirst($controller) . 'Controller';
           }
           if (strpos($action, 'action') === false) {
               $action = 'action'.ucfirst($action);
           }

           $mapping['method'] = $action;
           $handlerFile = implode('/',
               array_merge(array_slice($params, 0, count($params) - 2),
                   [$controller . '.php']));

           if (!file_exists($handlerFile)) {
               throw new UnknownClassException("{Unknown class {$handler}");
           }

           $classNamespace = BaseFileHelper::getNamespace($handlerFile);
           $className = '\\' . $classNamespace . '\\' . basename(str_replace('.php', '', $handlerFile));
           $mapping[] = App::createObject($className);


           $this->_controllerMap[$handler] = $mapping;
       }

       foreach ($this->_controllerMap[$handler] as $item => $value) {
           if (!is_numeric($item)) {
               $this->setProperty($item, $value);
           }
       }
       
       return end($this->_controllerMap[$handler]);
    }

    public function getMethod()
    {
        return $this->getProperties('method');
    }
}