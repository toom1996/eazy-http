<?php

namespace eazy\http;

use eazy\http\helpers\StringHelper;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD|Attribute::TARGET_FUNCTION)]
class Hook extends BaseObject
{
    public static $hookMap = [];

    // bind hook
    // 
    public static function bind($event, $callable)
    {
        self::$hookMap[$event][] = $callable;
//        foreach ($class->hookTags as $tag) {
//            self::$hookMap[$tag][] = $class;
//        }
//        var_dump(self::$hookMap);
    }
    
    public static function trigger($event, $owner)
    {
        $handlers = self::$hookMap[$event];
        foreach ($handlers as $handler) {
            $handler();
        }
//        App::info('tigger');
//        var_dump($tags);
//        foreach ($tags as $tag) {
//            $triggerClass = self::$hookMap[$tag];
//            foreach ($triggerClass as $cl) {
//                $cl->{$cl->event()[$event]}();
//            }
//        }
    }
}