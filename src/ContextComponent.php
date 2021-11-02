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
        return Context::put($this->getObjectId(), $value);
    }

    protected function getContext(): mixed
    {
        return Context::get($this->getObjectId());
    }

    protected function setAttributes($key, $value)
    {
        Context::setAttributes($key, $value);
    }

    protected function getAttributes()
    {
        return Context::getAttributes() ?? [];
    }
}