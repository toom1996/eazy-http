<?php

namespace eazy\http\web;

use eazy\http\App;
use eazy\http\BaseController;
use eazy\http\Component;
use eazy\http\components\View;
use eazy\http\Eazy;

/**
 * @property \eazy\http\components\View $view
 */
class Controller extends \eazy\http\Controller
{
    protected ?string $layout = null;

    public function runAction($action)
    {
        if ($this->beforeAction($this->action)) {
            $result = call_user_func([$this, $action]);
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

    /**
     * Get view component from web controller.
     * @return \eazy\http\components\View
     */
    public function getView()
    {
        $view = Eazy::$component->view;
        if ($this->layout) {
            $view->setLayout($this->layout);
        }
        
        return $view;
    }
}