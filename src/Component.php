<?php

namespace eazy\http;

use eazy\helpers\BaseArrayHelper;
use eazy\http\App;
use eazy\http\components\UrlManager;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\UnknownClassException;
use Swoole\Coroutine;

/**
// * @property array $context
 * @property array $attributes
 * @property array $properties
 * @property integer $classId
 * @property integer $coroutineUid
 */
class Component extends BaseObject
{

    public $behaviors;


    public function __set(string $name, $value)
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

    public function event()
    {
        return [];
    }


    public function trigger($name, $sender = null)
    {
        App::info($this->behaviors());
        foreach ($this->behaviors() as $behavior) {
            App::info($behavior);
            App::$locator->event->execute($behavior, $name, $sender);
        }
//        App::$locator->event->
//        $this->ensureBehaviors();
//
//        $eventHandlers = [];
//        foreach ($this->_eventWildcards as $wildcard => $handlers) {
//            if (StringHelper::matchWildcard($wildcard, $name)) {
//                $eventHandlers = array_merge($eventHandlers, $handlers);
//            }
//        }
//
//        if (!empty($this->_events[$name])) {
//            $eventHandlers = array_merge($eventHandlers, $this->_events[$name]);
//        }
//
//        if (!empty($eventHandlers)) {
//            if ($event === null) {
//                $event = new Event();
//            }
//            if ($event->sender === null) {
//                $event->sender = $this;
//            }
//            $event->handled = false;
//            $event->name = $name;
//            foreach ($eventHandlers as $handler) {
//                $event->data = $handler[1];
//                call_user_func($handler[0], $event);
//                // stop further handling if the event is handled
//                if ($event->handled) {
//                    return;
//                }
//            }
//        }
//
//        // invoke class-level attached handlers
//        App::$locator->event->trigger($this, $name, $event);
    }

    public function ensureBehaviors()
    {
        if (!App::$locator->event->getBehaviors()) {
            foreach ($this->behaviors() as $name => $behavior) {
                $this->attachBehaviorInternal($name, $behavior);
            }
        }
    }

    public function on($name, $handler, $data = null, $append = true)
    {
        $this->ensureBehaviors();

        if (strpos($name, '*') !== false) {
            if ($append || empty($this->_eventWildcards[$name])) {
                $this->_eventWildcards[$name][] = [$handler, $data];
            } else {
                array_unshift($this->_eventWildcards[$name], [$handler, $data]);
            }
            return;
        }

        if ($append || empty($this->_events[$name])) {
            App::$locator->event->setEvent($handler, $data);
//            $this->_events[$name][] = [$handler, $data];
        } else {
            array_unshift($this->_events[$name], [$handler, $data]);
        }
    }

    private function attachBehaviorInternal($name, $behavior)
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

    public function behaviors()
    {
        return [];
    }

    public function __call($name, $params)
    {
        $proxyMethod = 'proxy' . $name;
        if (method_exists($this, $proxyMethod)) {
            echo 'proxy';
            return call_user_func_array([$this, $proxyMethod], $params);
        }

        throw new UnknownClassException('Getting unknown method: ' . get_class($this) . '::' . $proxyMethod);
    }
}