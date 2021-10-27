<?php

namespace eazy\http;

use eazy\helpers\BaseArrayHelper;
use eazy\http\base\BaseComponent;

class Request extends BaseComponent {

    
    protected function getContext()
    {
        return Context::get('request');
    }

    public function fd()
    {
        
    }
    
    public function get($name = '', $default = null)
    {
        $key = $name ? ".{$name}" : '';
        return $this->getValue($this->getContext(), "get{$key}", $default);
    }

    public function queryString($default = null)
    {
        return $this->getValue($this->getContext(), 'server.query_string', $default);
    }
}