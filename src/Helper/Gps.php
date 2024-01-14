<?php

namespace Macocci7\PhpPhotoGps\Helper;

use Macocci7\PhpPhotoGps\Helper\Config;
use Macocci7\PhpPhotoGps\Helper\Exif;

/**
 * Class for GPS Data Definition and Conversion.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Gps
{
    /**
     * @var bool    $configLoaded
     */
    private static bool $configLoaded = false;

    /**
     * init
     * @return  void
     */
    public static function init()
    {
        Config::load();
        self::$configLoaded = true;
    }

    /**
     * returns definition of GPS Tags
     * @param   string|null  $key = null
     * @return  mixed[]|null
     */
    public static function def(?string $key = null)
    {
        if (!self::$configLoaded) {
            self::init();
        }
        return Config::get($key); // @phpstan-ignore-line
    }

    /**
     * returns type definition of the tag
     * @param   string  $key
     * @return  string
     */
    public static function type(string $key)
    {
        return self::def($key)['type'] ?? null; // @phpstan-ignore-line
    }

    /**
     * returns count definition of the tag
     * @param   string  $key
     * @return  int
     */
    public static function count(string $key)
    {
        return self::def($key)['count'] ?? null; // @phpstan-ignore-line
    }

    /**
     * returns value definition of the tag
     * @param   string  $key
     * @return  string[]|null
     */
    public static function values(string $key)
    {
        return self::def($key)['values'] ?? null;  // @phpstan-ignore-line
    }

    /**
     * filters Exif Data.
     * @param   mixed[]     $exif
     * @return  mixed[]|null
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public static function filter(array $exif)
    {
        foreach ($exif as $key => $value) {
            if (!str_starts_with($key, 'GPS')) {
                unset($exif[$key]);
                continue;
            }
        }
        return self::convert($exif);
    }

    /**
     * converts GPS Data.
     * @param   mixed[]     $gps
     * @return  mixed[]|null
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public static function convert(array $gps)
    {
        $prefix = sprintf("exif%s.fields.", Exif::version());
        foreach ($gps as $key => $value) {
            $type = self::type($prefix . $key);
            $count = self::count($prefix . $key);
            if (self::isDefByte($prefix . $key)) {
                $separator = self::def($prefix . $key)['separator']; // @phpstan-ignore-line
                $gps[$key] = Exif::byte2ascii($value, $count, $separator); // @phpstan-ignore-line
                continue;
            }
            $gps[$key] = is_array($value)
            ? array_map(fn ($v) => Exif::stripNullByte($v), $value)
            : Exif::stripNullByte($value)   // @phpstan-ignore-line
            ;
        }
        return $gps;
    }

    /**
     * judges if the defined Type is BYTE.
     * @param   string  $key
     * @return  bool
     */
    public static function isDefByte(string $key)
    {
        return 0 === strcmp('BYTE', self::type($key));
    }

    /**
     * judges if the defined Type is SHORT.
     * @param   string  $key
     * @return  bool
     */
    public static function isDefShort(string $key)
    {
        return 0 === strcmp('SHORT', self::type($key));
    }

    /**
     * judges if the defined Type is ASCII.
     * @param   string  $key
     * @return  bool
     */
    public static function isDefAscii(string $key)
    {
        return 0 === strcmp('ASCII', self::type($key));
    }

    /**
     * judges if the defined Type is RATIONAL.
     * @param   string  $key
     * @return  bool
     */
    public static function isDefRational(string $key)
    {
        return 0 === strcmp('RATIONAL', self::type($key));
    }

    /**
     * judges if the defined Type is UNDEFINED.
     * @param   string  $key
     * @return  bool
     */
    public static function isDefUndefined(string $key)
    {
        return 0 === strcmp('UNDEFINED', self::type($key));
    }
}
