<?php

namespace eazy\http;

use Swoole\Coroutine;

class Context
{
    protected static $pool = [];

    static function get($key):mixed
    {
        $cid = Coroutine::getuid();
        if ($cid < 0)
        {
            return null;
        }
        if(isset(self::$pool[$cid][$key])){
            return self::$pool[$cid][$key];
        }
        return null;
    }

    static function put($key, $item)
    {
        $cid = Coroutine::getuid();
        if ($cid > 0)
        {
            self::$pool[$cid][$key] = $item;
        }

    }

    static function delete($key = null)
    {
        $cid = Coroutine::getuid();
        if ($cid > 0)
        {
            if($key){
                unset(self::$pool[$cid][$key]);
            }else{
                unset(self::$pool[$cid]);
            }
        }
    }

    public static function setAttributes($key, $value)
    {
        $cid = Coroutine::getuid();
        if ($cid > 0){
            self::$pool[$cid]['attributes'][$key] = $value;
        }
    }

    public static function getAttributes($key = null)
    {
        $cid = Coroutine::getuid();
        if ($key) {
            return self::$pool[$cid]['attributes'][$key];
        }

        return self::$pool[$cid]['attributes'];
    }

}