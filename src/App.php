<?php

namespace eazy\http;

use eazy\base\Exception;
use eazy\di\Di;
use eazy\Eazy;
use eazy\http\base\BaseApp;
use eazy\http\di\Container;
use eazy\http\exceptions\CoroutineException;
use eazy\http\exceptions\InvalidConfigException;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Symfony\Component\Console\Tester\TesterTrait;


class App extends BaseApp
{
    /**
     * @var \eazy\http\ServiceLocator
     */
    public static $component;

    /**
     * Context pool.
     * @var
     */
    public static $pool;

    /**
     * Alias map.
     * @var
     */
    private static $aliases;


    /**
     * Creates a new object using the given configuration.
     * Support create an object based on class name or configuration of params.
     * ```php
     * // create object based on class name.
     * $object = App::createObject('\eazy\http\Request');
     * $object = App::createObject(Request::class);
     *
     * // create object
     * @param $definition
     * @param  array  $params
     *
     * @return object
     * @throws \ReflectionException
     * @throws \eazy\http\exceptions\InvalidConfigException
     */
    public static function createObject($definition, $params = [])
    {
        Container::$instance->build();
    }

    /**
     * Get alias.
     *
     * @param        $alias
     * @param  bool  $throwException
     *
     * @return bool|string
     */
    public static function getAlias($alias, $throwException = true)
    {
        if (strpos($alias, '@') !== 0) {
            // not an alias
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root]
                    : static::$aliases[$root].substr($alias, $pos);
            }

            foreach (static::$aliases[$root] as $name => $path) {
                if (strpos($alias.'/', $name.'/') === 0) {
                    return $path.substr($alias, strlen($name));
                }
            }
        }

        return false;
    }


    /**
     * Registers a path alias.
     *
     * A path alias is a short name representing a long path (a file path, a URL, etc.)
     * For example, we use '@yii' as the alias of the path to the Yii framework directory.
     *
     * A path alias must start with the character '@' so that it can be easily differentiated
     * from non-alias paths.
     *
     * Note that this method does not check if the given path exists or not. All it does is
     * to associate the alias with the path.
     *
     * Any trailing '/' and '\' characters in the given path will be trimmed.
     *
     * See the [guide article on aliases](guide:concept-aliases) for more information.
     *
     * @param  string  $alias  the alias name (e.g. "@yii"). It must start with a '@' character.
     * It may contain the forward slash '/' which serves as boundary character when performing
     * alias translation by [[getAlias()]].
     * @param  string  $path  the path corresponding to the alias. If this is null, the alias will
     * be removed. Trailing '/' and '\' characters will be trimmed. This can be
     *
     * - a directory or a file path (e.g. `/tmp`, `/tmp/main.txt`)
     * - a URL (e.g. `http://www.yiiframework.com`)
     * - a path alias (e.g. `@yii/base`). In this case, the path alias will be converted into the
     *   actual path first by calling [[getAlias()]].
     *
     * @throws InvalidArgumentException if $path is an invalid alias.
     * @see getAlias()
     */
    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@'.$alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/')
                : static::getAlias($path);
            if ( ! isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [$alias => $path];
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [
                        $alias => $path, $root => static::$aliases[$root],
                    ];
                }
            } else {
                static::$aliases[$root][$alias] = $path;
                krsort(static::$aliases[$root]);
            }
        } elseif (isset(static::$aliases[$root])) {
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }

    public static function autoload($className)
    {
        if (isset(static::$classMap[$className])) {
            $classFile = self::$classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = self::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = self::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }
        ////
        include $classFile;

        if (!class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new UnknownClassException("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }
    
    public static function setContext($key, $value)
    {
        $cid = self::getUid();
        self::$pool[$cid][$key] = $value;
    }
    
    public static function getContext($key)
    {
        $cid = self::getUid();
        return self::$pool[$cid][$key];
    }
    
    public static function getUid()
    {
        $cid = Coroutine::getuid();
        if ($cid < 0) {
            throw new CoroutineException("Not in coroutine environment");
        }
        
        return $cid;
    }
}