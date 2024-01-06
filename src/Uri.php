<?php

namespace Macocci7\PhpPhotoGps;

use Macocci7\PhpPhotoGps\Config;

/**
 * Class for Uri matter.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Uri
{
    /**
     * @var string[]|null   $availableScheme
     */
    private static array|null $config;

    /**
     * init
     */
    public static function init()
    {
        if (is_null(self::$config)) {
            Config::load();
        }
        self::$config = Config::get();
    }

    public static function get(?string $key = null)
    {
        if (is_null(self::$config)) {
            self::init();
        }
        if (is_null($key)) {
            return self::$config;
        }
        if (!isset(self::config[$key])) {
            return null;
        }
        return self::config[$key];
    }

    /**
     * judges if the path is available uri or not
     * @param   string  $path
     * @return  bool
     */
    public static function isAvailable(string $uri)
    {
        foreach (self::get('availableScheme') as $scheme) {
            if (str_starts_with($uri, $scheme)) {
                return true;
            }
        }
        return false;
    }
}
