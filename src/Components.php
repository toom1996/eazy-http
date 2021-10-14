<?php

namespace eazy\http;

use eazy\base\BaseObject;
use eazy\http\components\UrlManager;
use eazy\Eazy;

class Components extends BaseObject
{
    const CORE_COMPONENTS = [
        'urlManager' => UrlManager::class
    ];

    public static function get($componentId)
    {
        if ($componentId === 'urlManager') {
            return UrlManager::getInstance();
        }
    }
}