<?php

namespace eazy\http;

class AssetBundle extends BaseObject
{
    public static function register()
    {
        App::$component->view->registerAssetBundle(get_called_class());
    }
}