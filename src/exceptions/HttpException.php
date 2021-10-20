<?php

namespace eazy\http\exceptions;

use eazy\base\Exception;

class HttpException extends Exception
{
    public function getName()
    {
        return 'Http Exception';
    }
}