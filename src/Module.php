<?php

namespace eazy\http;

use eazy\Eazy;
use eazy\http\components\Request;
use eazy\http\exceptions\InvalidConfigException;

class Module extends ServiceLocator
{

    public function runAction($path)
    {
        // If is register
        if (isset(App::$get->getUrlManager()->controllerMap[$path])) {
            $controller = App::$get->getUrlManager()->controllerMap[$path];
        }else{
            $controller = App::$get->getUrlManager()->setControllerMap($path);
        }

        $controller = Eazy::createObject($controller);
        if (is_object($controller) && $controller instanceof Controller) {
            return call_user_func([$controller, $controller->action]);
        }

        throw new InvalidConfigException("Unknown action.");
    }
}