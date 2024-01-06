<?php

namespace Macocci7\PhpPhotoGps;

/**
 * Class for Exif Data Handling
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Exif
{
    /**
     * converts BYTE data into human-readable array.
     * @param   string  $byte
     * @param   int     $count
     * @return  string[]
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
     * @return  string
     */
    public static function byte2ascii(
        string $byte,
        int $count,
        ?string $separator = ''
    ) {
        return implode($separator, self::byte2array($byte, $count));
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
        return (int) $values[0] / (int) $values[1];
    }

    /**
     * judges if the strings is RATIONAL data or not.
     * @param   string  $string
     * @return  bool
     */
    public static function isRational(string $string)
    {
        return preg_match("/^\d+\/\d+$/", $string);
    }

    /**
     * simplifies degrees.
     * @param   int|float   $degrees
     * @return  int|float
     */
    public static function simplifyDegrees(int|float $degrees)
    {
        return $degrees % 360 + $degrees - (int) $degrees;
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
