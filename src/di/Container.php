<?php

namespace eazy\http\di;

use eazy\http\Request;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{

    /**
     * @var \eazy\http\di\Container
     */
    public static $instance;
    /**
     * @var array Singleton objects.
     */
    private array $_singletons = [];

    public function set($class, $definition = [], array $params = [])
    {
        $this->_singletons[$class] = $this->normalizeDefinition($class, $definition);
        $this->_singletons[$class] = 
        self::$container->set($class, Eazy::createObject($this->_singletons[$class]));
        //        $this->_params[$class] = $params;
        unset($this->_singletons[$class]);
        return $this;
    }

    public function get($class, $params = [], $config = [])
    {
        if (isset($this->_singletons[$class])) {
            // singleton
            return $this->_singletons[$class];
        }
    }

    public function build($class, $params)
    {

    }
}