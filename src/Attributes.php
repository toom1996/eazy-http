<?php

namespace eazy\http;

use eazy\http\helpers\FileHelper;

class Attributes extends BaseObject
{
    public static $instance;
    
    private array $_map = [];

    public function init()
    {
        self::$instance = $this;
        $this->scan();
        parent::init(); // TODO: Change the autogenerated stub
    }
    
    public function scan()
    {
        FileHelper::findDirectories()
    }
}