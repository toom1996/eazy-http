<?php

namespace eazy\http;

/**
 * @property array $behaviros
 */
class Event extends ContextComponent
{
    public $eventMap;



    public function execute($behavior, $name, $sender)
    {
        if (!isset($this->eventMap[$behavior])) {
            $this->eventMap[$behavior] = App::createObject($behavior);
        }


        if (isset($this->eventMap[$behavior]->event()[$name])) {
            $this->eventMap[$behavior]->{$this->eventMap[$behavior]->event()[$name]}($sender);
        }
    }
}