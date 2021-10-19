<?php

namespace eazy\http\controllers;

use eazy\Eazy;
use eazy\http\App;
use eazy\http\Controller;

class ErrorController extends Controller
{
    public function actionIndex()
    {
        return $this->render('@eazy/views/error/exception', [
            'exception' => App::$get->getErrorHandler()->exception,
            'handler' => App::$get->getErrorHandler(),
        ]);
    }
}