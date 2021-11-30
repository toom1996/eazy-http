<?php

namespace eazy\http\controllers;

use eazy\http\App;
use eazy\http\Eazy;
use eazy\http\Module;
use eazy\http\web\Controller;

class ErrorController extends Controller
{
    protected ?string $layout = '@eazy/views/layouts/error';

    public function actionIndex()
    {
        $errorHandler = Eazy::$component->errorHandler;
        //@eazy/views/error/exception
        return $this->view->render('@eazy/views/error/exception', [
            'exception' => $errorHandler->exception,
            'handler' => $errorHandler,
        ]);
    }
}