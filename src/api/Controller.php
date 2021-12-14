<?php

namespace eazy\api;

use eazy\http\Eazy;

class Controller extends \eazy\http\web\Controller
{
    public function afterAction($action, &$result)
    {
        echo __FUNCTION__;
        if (is_array($result)) {
            $result = json_encode($result);
        }
    }
}