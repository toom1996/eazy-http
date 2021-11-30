<?php

namespace eazy\http;

use eazy\helpers\BaseArrayHelper;
use eazy\http\components\UrlManager;
use eazy\http\Eazy;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;
use Swoole\Coroutine;

/**
// * @property array $context
 * @property array $attributes
 * @property array $properties
 * @property integer $classId
 * @property integer $coroutineUid
 */
#[\Attribute(\Attribute::TARGET_FUNCTION)]
class Component extends BaseObject
{
    /**
     * Is bootstrap component.
     * @var bool 
     */
    public bool $bootstrap = true;

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            // set property
            $this->$setter($value);
            return;
        }

        if (method_exists($this, 'get' . $name)) {
            throw new InvalidConfigException('Setting read-only property: ' . get_class($this) . '::' . $name);
        }

        throw new UnknownClassException('Setting unknown property: ' . get_class($this) . '::' . $name);
    }

    public function __get(string $name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            // read property, e.g. getName()
            return $this->$getter();
        }

        if (method_exists($this, 'set' . $name)) {
            throw new InvalidConfigException('Getting write-only property: ' . get_class($this) . '::' . $name);
        }

        throw new UnknownClassException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }
}