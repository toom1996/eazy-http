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
       App::info(get_called_class() . ' Initilize');
    }
    
    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }
}