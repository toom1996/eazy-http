<?php

namespace eazy\http;

/**
 * @property array $behaviros
 */
class Event extends ContextComponent
{
    public static $behaviors;

    public static $event;

    public function getBehaviors()
    {
        return $this->getProperties('behaviors');
    }

    public function setEvent($handler, $data)
    {
        $originEvent = $this->properties['event'];
        $originEvent[] = [$handler, $data];
        $this->properties['event'] = $originEvent;
    }
    

    public function attachBehaviorInternal($name, $behavior)
    {
        // not actionFilter
        if (!($behavior instanceof Behavior)) {
            $behavior = Eazy::createObject($behavior);
        }

        if (is_int($name)) {
            $behavior->attach($this);
            $this->_behaviors[] = $behavior;
        } else {
            if (isset($this->_behaviors[$name])) {
                $this->_behaviors[$name]->detach();
            }
            $behavior->attach($this);
            $this->_behaviors[$name] = $behavior;
        }

        return $behavior;
    }

    public static function triggerEvent()
    {
       
    }
}