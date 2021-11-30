<?php

namespace eazy\http\components;

use eazy\Eazy;
use eazy\http\App;
use eazy\http\base\BaseComponent;

class RequestHandler extends BaseComponent
{
    public function handleRequest()
    {
        [$handler, $params] = Eazy::getRequest()->resolve();
        $result = $this->runAction($handler);
//        $result = $this->runAction($handler);
//        $response = $this->getResponse();
//        if ($result !== null) {
//            $response->content = $result;
//        }
//
//        return $response;
    }
}