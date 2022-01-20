<?php

namespace eazy\api;

use eazy\http\App;
use eazy\http\behaviors\ApiBehavior;
use eazy\http\filters\CorsFilter;


class Controller extends \eazy\http\web\Controller
{

//    public function behaviors()
//    {
//        return [
//            ApiBehavior::class,
////            CorsFilter::class
//        ];
//    }

//    public function afterAction($action, &$result)
//    {
//        echo __FUNCTION__;
//        if (is_array($result)) {
//            $result = json_encode($result);
//        }
//        parent::afterAction($action, $result);
//    }
}