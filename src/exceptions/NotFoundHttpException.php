<?php

namespace eazy\http\exceptions;

use Throwable;

class NotFoundHttpException extends HttpException
{
    public function __construct(
        $message = "",
        $code = 404,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
    
}