<?php

namespace eazy\http;

use eazy\http\base\BaseComponent;
use Swoole\Coroutine;

/**
 * @property mixed $context
 * @property array $attributes
 */
class ContextComponent extends BaseComponent
{
    protected function setContext($value)
    {
        return Context::put($this->getObjectId().'context', $value);
    }

    protected function getContext(): mixed
    {
        return Context::get($this->getObjectId().'context');
    }

    protected function setAttributes($key, $value)
    {
        $classid = spl_object_id($this);
        echo(__FUNCTION__). PHP_EOL;
        echo($key). PHP_EOL;
        var_dump($value). PHP_EOL;
        echo($classid . '=====================') . PHP_EOL;
        $cid = Coroutine::getuid();
        if ($cid > 0){
            Context::$pool[$cid][$classid]['attributes'][$key] = $value;
        }
    }

    protected function getAttributes()
    {
        $classid = spl_object_id($this);
        $cid = Coroutine::getuid();
        echo(__FUNCTION__). PHP_EOL;
        var_dump(Context::$pool[$cid][$classid]['attributes'] ?? []). PHP_EOL;
        echo($classid . '=====================') . PHP_EOL;
        return Context::$pool[$cid][$classid]['attributes'] ?? [];
    }
}