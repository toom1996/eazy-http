<?php

namespace eazy\http;

use eazy\http\components\Request;

class Module extends ServiceLocator
{
    /**
     * 
     *
     * @param  string  $id
     *
     * @return Request
     * @throws exceptions\InvalidConfigException
     */
    public static function component(string $id)
    {
        if (!App::$app->has($id)) {
            App::$app->set($id);
        }

        return App::$app->get($id);
    }
}