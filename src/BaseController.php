<?php

namespace eazy\http;

class BaseController extends Component
{

    public function runAction($action)
    {
        $this->beforeAction($action);
        return $this->{$action}();
    }


    public function beforeAction($action)
    {
        echo 'beforeAction -> ' . $action;
    }
}