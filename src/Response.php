<?php

namespace eazy\http;

use eazy\http\base\BaseComponent;

class Response extends BaseComponent
{
    public function init()
    {
        echo 'iit';
    }

    /**
     *
     * @return \Swoole\Http\Response
     */
    public function getContext()
    {
        return Context::get('response');
    }

    public function send($content = null)
    {
        $this->getContext()->end($content);
    }
}