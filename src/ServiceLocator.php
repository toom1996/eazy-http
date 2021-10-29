<?php

namespace eazy\http;

use eazy\base\BaseObject;
use eazy\Eazy;
use eazy\http\exceptions\InvalidConfigException;

/**
 * @property \eazy\http\Request $request
 */
class ServiceLocator extends Component
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

    /**
     * 实例化组件
     *
     * @param  string  $id
     * @param  null    $definition
     *
     * @throws InvalidConfigException
     */
    public function set(string $id, $definition = null)
    {
        unset($this->_components[$id]);
        // set('className', array());
        // set('className', object());
        if (is_string($id)) {
            $this->_components[$id] = Eazy::createObject(App::$config['components'][$id]['class'], $definition);
        }elseif (is_array($definition)) {
            $this->_components[$id] = Eazy::createObject($definition);
        }elseif(is_object($definition)){
            if (isset(App::$config['components'][$id]['class'])) {
                $this->_components[$id] = Eazy::createObject(App::$config['components'][$id]);
            }else{
                $this->_components[$id] = $definition;
            }
        }elseif (is_null($definition)) {
            $this->_components[$id] = Eazy::createObject(App::$config['components'][$id]);
        }
    }

    public function __get(string $name)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }

        return parent::__get($name);
    }
}