<?php

namespace eazy\http\base;

use eazy\base\BaseObject;
use eazy\helpers\BaseArrayHelper;
use eazy\http\Context;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;

/**
 * @property integer $objectId
 */
class BaseComponent extends BaseObject
{
    /**
     * Is bootstrap component.
     * @var
     */
    public bool $bootstrap = false;

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
    
    
    protected function getValue($array, $key, $default = null)
    {
        return BaseArrayHelper::getValue($array, $key, $default);
    }

    protected function getObjectId(): int
    {
        return spl_object_id($this);
    }
    
}