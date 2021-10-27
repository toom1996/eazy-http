<?php

namespace eazy\http\base;

use eazy\helpers\BaseArrayHelper;

abstract class BaseComponent
{

    public function __construct()
    {
        $this->init();
    }
    
    public function init(){}

    protected function getContext(){}
    
    protected function getValue($array, $key, $default = null)
    {
        return BaseArrayHelper::getValue($array, $key, $default);
    }
    
}