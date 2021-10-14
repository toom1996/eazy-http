<?php

namespace eazy\http;

class RouterDispatcher
{
    public function process()
    {
        echo 123;
    }

    public static function getDispatcher()
    {
        echo 123;
//        return Components::getUrlManager();
    }
}