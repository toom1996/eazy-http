<?php


namespace eazy\http;


use app\controllers\SiteController;
use eazy\console\StdoutLogger;
use eazy\Eazy;
use eazy\helpers\BaseFileHelper;
use eazy\http\di\Container;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;
use Swoole\FastCGI\Record\Stdout;

/**
 * @property string $method
 * @property string $action
 */
class Controller extends Component
{
    /**
     * Controller map.
     * @var array
     */
    private array $_controllerMap = [];

    private array $_controllerMapParams = [];
    
    
    public function runAction($path)
    {
        $controller = $this->setControllerMap($path);
        if (is_object($controller) && $controller instanceof Controller) {
            return $controller->runAction($controller->method);
        }

        throw new InvalidConfigException("Run action fail.");
    }

    private function setControllerMap($handler)
    {
        if (isset($this->_controllerMap[$handler])) {
            $controllerMap = $this->_controllerMap[$handler];
        }else{
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

            echo 'new Controller' . PHP_EOL;
            $this->_controllerMap[$handler] = [
                $className, $config
            ];
        }

        echo 'return controller';
        var_dump($this->_controllerMap[$handler]);
        return App::createObject(...$this->_controllerMap[$handler]);
    }

    public function setAction($action)
    {
        $this->setAttribute('action', $action);
    }

    public function setMethod($method)
    {
        $this->setAttribute('method', $method);
    }

    public function getMethod()
    {
        return $this->attributes['method'];
    }

    public function getAction()
    {
        return $this->attributes['action'];
    }
}