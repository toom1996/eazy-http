<?php

namespace eazy\http;

use eazy\base\BaseObject;
use eazy\Eazy;
use eazy\http\exceptions\InvalidConfigException;

class ServiceLocator extends BaseObject
{

    private array $_components = [];

    public function has(string $id)
    {
        if ( ! isset($this->_components[$id]) && ! Eazy::$container->get($id)) {
            return false;
        }

        return true;
    }

    public function get(string $id)
    {
        if (isset($this->_components[$id])) {
            return $this->_components[$id];
        }

        if (Eazy::$container->has($id)) {
            return Eazy::$container->get($id);
        }

        throw new InvalidConfigException("Unknown component ID: {$id}");
    }

    public function set(string $id, $definition = null)
    {
        unset($this->_components[$id]);


        if (is_array($definition)) {
            // e.g Eazy::$app->set('foo', ['class' => foo\bar, 'a' => 'b'])
            // If has class, it will be overwrite all component attributes.
            if (isset($definition['class'])) {
                $this->_components[$id] = Eazy::createObject($definition);
            }
        }elseif (is_null($definition)) {
            var_dump(Application::$config[$id]);
        }
    }
}