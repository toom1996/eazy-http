<?php

namespace eazy\http;

use eazy\http\base\BaseComponent;

/**
 * @property mixed $context
 */
class ContextComponent extends BaseComponent
{
    protected function getContext(): mixed
    {
        return Context::get($this->getObjectId());
    }
}