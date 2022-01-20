<?php

namespace eazy\http\behaviors;

use eazy\http\App;
use eazy\http\components\Response;
use eazy\http\ContextComponent;
use eazy\http\Controller;

class ApiBehavior extends ContextComponent
{

    public function event()
    {
        return [
            \eazy\http\Response::EVENT_BEFORE_SEND => 'beforeSend'
        ];
    }

    public function beforeSend($sender)
    {
        echo '@@@@@@@@@@@@@@@@@@@@@@@@@@';
        var_dump($sender);
        var_dump(__FUNCTION__);
    }
}