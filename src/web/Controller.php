<?php

namespace eazy\http\web;

use eazy\http\App;
use eazy\http\BaseController;
use eazy\http\Component;

class Controller extends \eazy\http\Controller
{

    public function runAction($action)
    {
        if ($this->beforeAction($this->action)) {
            $result = $this->{$action}();
        }
        $this->afterAction($action, $result);

        return $result;
    }


    public function beforeAction($action)
    {
        // TODO trigger `beforeAction` event.
        return true;
    }

    public function afterAction($action, $result)
    {
        echo PHP_EOL;
        echo 'afterAction -> ' . $action;
        echo 'afterAction -> ' . $result;
    }
    
    public function getView()
    {
        return App::$component->view;
    }
}