<?php

namespace eazy\http\base;

use eazy\base\BaseObject;
use eazy\di\Di;

class BaseApp extends BaseObject
{
    private static $test;

    public function init()
    {
        echo __CLASS__;
        self::$test = new Di();
        parent::init(); // TODO: Change the autogenerated stub
    }

    public static function getContainer()
    {
        var_dump(self::$test);
    }
}