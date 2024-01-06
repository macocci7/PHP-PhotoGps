<?php

namespace Macocci7\PhpPhotoGps;

use Macocci7\PhpPhotoGps\Config;
use Macocci7\PhpPhotoGps\Exif;

/**
 * Class for GPS Data Definition and Conversion.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Gps
{
    /**
     * @var array|null  $data
     */
    private static array|null $data;

    /**
     * @var array|null  $def
     */
    private static array|null $def;

    /**
     * init
     */
    public static function init()
    {
        Config::load();
        self::$def = Config::get('fields');
    }

    /**
     * returns definitino of GPS Tags
     * @param   string  $key = null
     * @return  mixed[]
     */
    public static function def(?string $key = null)
    {
        if (is_null($key)) {
            return self::$def;
        }
        if (!isset(self::$def[$key])) {
            return null;
        }
        return self::$def[$key];
    }

    /**
     * returns type definition of the tag
     * @param   string  $key
     * @return  string
     */
    public static function type(string $key)
    {
        return self::def($key)['type'] ?? null;
    }

    /**
     * returns count definition of the tag
     * @param   string  $key
     * @return  int
     */
    public static function count(string $key)
    {
        return self::def($key)['count'] ?? null;
    }

    /**
     * returns value definition of the tag
     * @param   string  $key
     * @return  string
     */
    public static function value(string $key)
    {
        if (!isset(self::data[$key])) {
            return null;
        }
    }

    public static function unpackByte(string $key)
    {
        $type = self::type($key);
        if (is_null($type)) {
            return null;
        }
        $count = self::count($key);
        $value = self::value($key);
        if (is_null($value)) {
            return null;
        }
        return unpack("C" . $count, $value);
    }

    public static function clear()
    {
        self::$data = null;
    }

    public static function get(string $path)
    {
    }

    public static function filter(array $exif)
    {
        foreach ($exif as $key => $value) {
            if (!str_starts_with($key, 'GPS')) {
                unset($exif[$key]);
                continue;
            }
            if (!isset(self::$def[$key])) {
                continue;
            }
        }
        return self::convert($exif);
    }

    public static function convert(array $gps)
    {
        foreach ($gps as $key => $value) {
            $type = self::type($key);
            $count = self::count($key);
            if (self::isDefByte($key)) {
                $separator = self::def($key)['separator'];
                $gps[$key] = Exif::byte2ascii($value, $count, $separator);
                continue;
            }
            $gps[$key] = is_array($value)
            ? array_map(fn ($v) => Exif::stripNullByte($v), $value)
            : Exif::stripNullByte($value)
            ;
        }
        return $gps;
    }

    public static function isDefByte($key)
    {
        return 0 === strcmp('BYTE', self::type($key));
    }

    public static function isDefShort($key)
    {
        return 0 === strcmp('SHORT', self::type($key));
    }

    public static function isDefAscii($key)
    {
        return 0 === strcmp('ASCII', self::type($key));
    }

    public static function isDefRational($key)
    {
        return 0 === strcmp('RATIONAL', self::type($key));
    }

    public static function isDefUndefined($key)
    {
        return 0 === strcmp('UNDEFINED', self::type($key));
    }
}
