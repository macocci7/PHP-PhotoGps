<?php

namespace Macocci7\PhpPhotoGps;

use Nette\Neon\Neon;

/**
 * Config operator.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Config
{
    /**
     * @var mixed[] $conf
     */
    private static array $conf = [];

    /**
     * loads config from a file
     * @return  void
     */
    public static function load()
    {
        $class = debug_backtrace()[1]['class']; // @phpstan-ignore-line
        $cl = self::className($class);
        $path = __DIR__ . '/../conf/' . $cl . '.neon';
        self::$conf[$class] = Neon::decodeFile($path);
    }

    /**
     * returns just the class name splitted parent namespace
     * @param   string  $class
     * @return  string
     */
    public static function className(string $class)
    {
        $pos = strrpos($class, '\\');
        if ($pos) {
            return substr($class, $pos + 1);
        }
        return $class;
    }

    /**
     * returns config data
     * @param   string  $key = null
     * @return  mixed
     * @thrown  \Exception
     */
    public static function get(?string $key = null)
    {
        $class = debug_backtrace()[1]['class']; // @phpstan-ignore-line
        if (!self::$conf[$class]) {
            throw new \Exception("Config of $class not found.");
        }
        if (is_null($key)) {
            return self::$conf[$class];
        }
        if (!self::$conf[$class][$key]) { // @phpstan-ignore-line
            throw new \Exception("$key not found.");
        }
        return self::$conf[$class][$key];
    }
}
