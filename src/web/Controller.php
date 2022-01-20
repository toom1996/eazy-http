<?php

namespace eazy\http\web;

use eazy\http\App;
use eazy\http\BaseController;
use eazy\http\Component;
use eazy\http\components\View;
use eazy\http\ContextComponent;
use eazy\http\Hook;
use eazy\http\Sender;

/**
 * @property \eazy\http\components\View $view
 */
class Controller extends ContextComponent
{
    /**
     * @param  \eazy\http\Sender  $sender
     *
     * @return \eazy\http\Sender
     */
    public function run($request, $response)
    {
        if ($this->beforeAction($sender)) {
            $sender->data = call_user_func([$this, App::$locator->controller->getMethod()]);
        }
        $this->afterAction();

        return $sender;
    }


    public function beforeAction(): bool
    {
        Hook::trigger('hook.beforeAction', $this);
        $this->trigger(\eazy\http\Controller::EVENT_BEFORE_ACTION);
        return true;
    }

    public function afterAction($sender)
    {
        echo PHP_EOL;
        $this->trigger(\eazy\http\Controller::EVENT_AFTER_ACTION, $sender);
        Hook::trigger('hook.afterAction', $this);
    }

    /**
     * Get view component from web controller.
     * @return \eazy\http\components\View
     */
    public function getView()
    {
        $view = App::$locator->view;
        if ($this->layout) {
            $view->setLayout($this->layout);
        }
        
        return $view;
    }
}