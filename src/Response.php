<?php

namespace eazy\http;

use eazy\http\base\BaseComponent;

class Response extends BaseComponent
{
    public function init()
    {
        echo 'iit';
    }

    public function send($content = null)
    {
        $this->getContext()->end($content);
    }

    public function setContent()
    {
        
    }
}