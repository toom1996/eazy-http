<?php

namespace eazy\http;

use eazy\base\BaseObject;
use eazy\Eazy;
use eazy\http\base\BaseComponent;
use eazy\http\di\Container;
use eazy\http\exceptions\InvalidConfigException;

/**
 * @property \eazy\http\Request $request
 * @property \eazy\http\Response $response
 * @property \eazy\http\components\View $view
 * @property \eazy\http\Controller $controller
 * @property \eazy\http\components\ErrorHandler $errorHandler
 * @property \eazy\http\Router $router
 * @property \eazy\http\databases\DbConnection $db
 */
class ServiceLocator extends BaseComponent
{
    
    public function has(string $id)
    {
        return Container::$instance->has($id);
    }

    public function get(string $id)
    {
        return Container::$instance->get($id);
    }

    public function __get(string $name)
    {

        if ($this->has($name)) {
            return $this->get($name);
        }
        return parent::__get($name);
    }
}