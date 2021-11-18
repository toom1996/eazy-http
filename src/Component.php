<?php

namespace eazy\http;

use eazy\helpers\BaseArrayHelper;
use eazy\http\components\UrlManager;
use eazy\Eazy;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;

/**
 * @property array $context
 */
class Component extends BaseObject
{
    /**
     * Is bootstrap component.
     * @var
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
    
    protected function setAttributes()
    {
        
    }

    protected function setContext($key, $value)
    {
        $oid = $this->getObjectId();
        if (!isset(App::$pool[App::getUid()][$oid])) {
            App::$pool[App::getUid()][$oid] = (Object)[];
        }
        App::$pool[App::getUid()][$oid]->{$key} = $value;
    }

    protected function getContext()
    {
        $oid = $this->getObjectId();
        if (!isset(App::$pool[App::getUid()][$this->getObjectId()])) {
            App::$pool[App::getUid()][$oid] = (Object)[];
            var_dump((Object)[]);
            return null;
        }

        return App::$pool[App::getUid()][$this->getObjectId()];
    }

    protected function getObjectId()
    {
        return spl_object_id($this);
    }
}