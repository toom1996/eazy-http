<?php

namespace eazy\http;

use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;
use Swoole\Coroutine;

/**
 * @property array $properties
 * @property integer $classId 
 */
class ContextComponent extends Component
{

    /**
     * Overwrite class component `__set` method.
     * @param  string  $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
            return;
        }

        $this->setProperty($name, $value);
        return;
    }

    /**
     * Overwrite class component `__get` method.
     * @param  string  $name
     */
    public function __get(string $name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        
       return $this->getProperties($name);
    }

    /**
     * Returns class id.
     * https://www.php.net/manual/zh/function.spl-object-id.php
     * @return int
     */
    public function getClassId(): int
    {
        return spl_object_id($this);
    }

    /**
     * Returns coroutine uid.
     * https://wiki.swoole.com/#/coroutine/coroutine?id=getcid
     * @return int
     */
    public function getCoroutineUid(): int
    {
        return Coroutine::getuid();
    }

    /**
     * Returns cortounie properties.
     * @return array
     */
    public function getProperties($key = null): array
    {
        if ($key) {
            return Eazy::$attributes[$this->coroutineUid][$this->classId][$key] ?? [];
        }

        return Eazy::$attributes[$this->coroutineUid][$this->classId] ?? [];
    }

    /**
     * Returns coroutine property with key.
     * @param string $key property name.
     *
     * @return mixed|null
     */
    public function getProperty(string $key): mixed
    {
        return $this->properties[$key] ?? null;
    }

    /**
     * Set coroutine property.
     * @param string $key property key.
     * @param mixed $value property value. allowed string, array, object.
     *
     * @return mixed
     */
    public function setProperty(string $key, $value)
    {
        return Eazy::$attributes[$this->coroutineUid][$this->classId][$key] = $value;
    }
}