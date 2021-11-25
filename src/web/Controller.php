<?php

namespace eazy\http\web;

use eazy\http\BaseController;
use eazy\http\Component;

class Controller extends BaseController
{

    public function runAction($action)
    {
        $this->beforeAction($action);
        $result = $this->{$action}();
        $this->afterAction($action, $result);

        return $result;
    }


    public function beforeAction($action)
    {
        echo 'beforeAction -> ' . $action;
    }

    public function afterAction($action, $result)
    {
        echo PHP_EOL;
        echo 'afterAction -> ' . $action;
        echo 'afterAction -> ' . $result;
    }
}