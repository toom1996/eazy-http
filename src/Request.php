<?php

namespace eazy\http;

use eazy\helpers\BaseArrayHelper;

class Request {

    public function __construct()
    {
    }

    public function fd()
    {
        
    }
    
    public function get()
    {
        return \eazy\http\Context::get('request');
    }

    public function queryString()
    {
        return $this->get()->server['query_string'];
    }
}