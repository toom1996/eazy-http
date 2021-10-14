<?php

namespace eazy\http\components;

class UrlManager
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
    
    public function handler()
    {
        
    }
}