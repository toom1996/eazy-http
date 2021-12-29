<?php

namespace eazy\api;

use eazy\http\Eazy;
use eazy\http\filter\CorsFilter;

class Controller extends \eazy\http\web\Controller
{

    public function behaviors()
    {
        return [
            CorsFilter::class
        ];
    }

    public function event()
    {
        
    }
    
    public function afterAction($action, &$result)
    {
        echo __FUNCTION__;
        if (is_array($result)) {
            $result = json_encode($result);
        }
    }
}