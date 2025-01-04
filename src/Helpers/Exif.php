<?php

namespace Macocci7\PhpPhotoGps\Helpers;

use Macocci7\PhpPhotoGps\Helpers\Uri;

/**
 * Class for Exif Data Handling
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Exif
{
    /**
     * @var string|null $version
     */
    private static string|null $version = null;

    /**
     * constructor
     */
    private function __construct()
    {
    }

    /**
     * returns ExifVersion
     * @return  string|null
     */
    public static function version(?string $version = null)
    {
        if (!is_null($version)) {
            self::$version = $version;
        }
        return self::$version;
    }

    /**
     * returns EXIF data from a file.
     * @param   string  $path
     * @return  mixed[]|null
     * @thrown  \Exception
     */
    public static function get(string $path)
    {
        if (!is_readable($path) && !Uri::isAvailable($path)) {
            throw new \Exception("The file is not readable.");
        }
        if (Uri::isAvailable($path)) {
            if (!ini_get('allow_url_fopen')) {
                ini_set('allow_url_fopen', '1');
            }
        }
        $exif = exif_read_data(
            file: $path,
            required_sections: null,
            as_arrays: true,
            read_thumbnail: false,
        );
        self::$version = $exif['EXIF']['ExifVersion'] ?? null;
        return $exif['GPS'] ?? null;
    }

    /**
     * converts BYTE data into human-readable array.
     * @param   string  $byte
     * @param   int     $count
     * @return  string[]|false
     */
    public static function byte2array(string $byte, int $count)
    {
        return unpack("C" . $count, $byte);
    }

    /**
     * converts BYTE data into human-readable ascii.
     * @param   string  $byte
     * @param   int     $count
     * @param   string  $separator = ''
     * @return  string|false
     */
    public static function byte2ascii(
        string $byte,
        int $count,
        string $separator = ''
    ) {
        $a = self::byte2array($byte, $count);
        return false === $a ? $a : implode($separator, $a);
    }

    /**
     * converts rational data into float.
     * @param   string  $rational
     * @return  float|null
     */
    public static function rational2Float(string $rational)
    {
        if (!self::isRational($rational)) {
            return null;
        }
        $values = explode("/", $rational);
        if (0 === (int) $values[1]) {
            return null;
        }
        return (float) ((int) $values[0] / (int) $values[1]);
    }

    /**
     * judges if the strings is RATIONAL data or not.
     * @param   string  $string
     * @return  bool
     */
    public static function isRational(string $string)
    {
        return preg_match("/^\d+\/\d+$/", $string) > 0;
    }

    /**
     * simplifies degrees.
     * @param   int|float   $degrees
     * @return  int|float
     */
    public static function simplifyDegrees(int|float $degrees)
    {
        return (int) $degrees % 360 + $degrees - (int) $degrees;
    }

    /**
     * returns strings with NULL BYTEs stripped.
     * @param   string  $value
     * @return  string
     */
    public static function stripNullByte(string $value)
    {
        return str_replace("\0", '', $value);
    }
}
