<?php

namespace Macocci7\PhpPhotoGps\Helper;

use Macocci7\PhpPhotoGps\Helper\Config;

/**
 * Class for Uri matter.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Uri
{
    /**
     * @var mixed[]|null   $config
     */
    private static array|null $config;

    /**
     * init
     * @return  void
     */
    public static function init()
    {
        if (!isset(self::$config)) {
            Config::load();
        }
        self::$config = Config::get();  // @phpstan-ignore-line
    }

    /**
     * returns config.
     * @param   string  $key = null
     * @return  mixed[]|null
     */
    public static function get(?string $key = null)
    {
        if (!isset(self::$config)) {
            self::init();
        }
        if (is_null($key)) {
            return self::$config;
        }
        if (!isset(self::$config[$key])) {
            return null;
        }
        return self::$config[$key]; // @phpstan-ignore-line
    }

    /**
     * judges if the path is available uri or not
     * @param   string  $uri
     * @return  bool
     */
    public static function isAvailable(string $uri)
    {
        $schemes = self::get('availableScheme');
        if (is_null($schemes)) {
            return false;
        }
        foreach ($schemes as $scheme) {
            if (str_starts_with($uri, $scheme)) {   // @phpstan-ignore-line
                return true;
            }
        }
        return false;
    }
}
