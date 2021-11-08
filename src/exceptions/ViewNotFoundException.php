<?php

namespace eazy\http\exceptions;

use eazy\base\Exception;

class ViewNotFoundException extends Exception
{

    public function getName()
    {
        return 'View not Found';
    }
}