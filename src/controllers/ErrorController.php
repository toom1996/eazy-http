<?php

namespace eazy\http\controllers;

use eazy\Eazy;
use eazy\http\App;
use eazy\http\Controller;
use eazy\http\Module;

class ErrorController extends Module
{
    public $layout = '@eazy/views/layouts/error';

    public function actionIndex()
    {
        //@eazy/views/error/exception
        return App::$component->view->render('@eazy/views/error/exception', [
            'exception' => $this->errorHandler->exception,
            'handler' => $this->errorHandler,
        ]);
    }
}