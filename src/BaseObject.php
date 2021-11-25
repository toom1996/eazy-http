<?php

namespace eazy\http;

class BaseObject
{
    /**
     * BaseObject constructor.
     *
     * @param  array  $config
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            self::configure($this, $config);
        }
        $this->init();
    }

    public function init()
    {
       
    }
    
    public static function configure($object, $properties)
    {
        echo __FUNCTION__;
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }
}