<?php

namespace eazy\http;

class Module extends ServiceLocator
{
    public function getComponet(string $id)
    {
        if ($this->has($id)) {
            return $this->get($id);
        }
        
        $this->set($id);
        //        var_dump(self::$app);
    }
}