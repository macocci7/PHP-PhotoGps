<?php

namespace Macocci7\PhpPhotoGps\Traits;

use Macocci7\PhpPhotoGps\Helpers\Config;
use Macocci7\PhpPhotoGps\Helpers\Exif;

trait AttributeTrait
{
    /**
     * @var string  $lang
     */
    private string $lang;

    /**
     * @var mixed[]     $units
     */
    private array $units;

    /**
     * sets specified language, returns current lang with no param.
     * @param   string|null  $lang = null
     * @return  self|string
     * @thrown  \Exception
     */
    public function lang(string|null $lang = null)
    {
        if (is_null($lang)) {
            return $this->lang;
        }
        if (!isset($this->units[$lang])) {
            throw new \Exception("$lang is not available.");
        }
        $this->lang = $lang;
        return $this;
    }

    /**
     * returns supported langages.
     * @return  string[]
     */
    public function langs()
    {
        return array_keys($this->units);
    }

    /**
     * sets format, or returns current format without param
     * @param   string  $format = null
     * @return  self|string
     */
    public function format(?string $format = null)
    {
        if (is_null($format)) {
            return $this->units[$this->lang()]['geo']['format']; // @phpstan-ignore-line
        }
        $this->units[$this->lang()]['geo']['format'] = $format; // @phpstan-ignore-line
        return $this;
    }

    /**
     * resets format as default
     * @return  self
     */
    public function resetFormat()
    {
        // @phpstan-ignore-next-line
        $this->format(Config::get('units')[$this->lang()]['geo']['format']);
        return $this;
    }

    /**
     * converts GPS sexagesimal numbers into a decimal number
     * @param   string[]   $s
     * @return  float|null
     */
    public function s2d(array $s)
    {
        if (count($s) < 3) {
            return null;
        }
        if (
               !Exif::isRational($s[0])
            || !Exif::isRational($s[1])
            || !Exif::isRational($s[2])
        ) {
            return null;
        }
        return (float) (
              Exif::rational2Float($s[0])
            + Exif::rational2Float($s[1]) / 60
            + Exif::rational2Float($s[2]) / 3600
        );
    }

    /**
     * converts a GPS decimal number into sexagesimal numbers
     * @param   float   $d
     * @return  string[]|null
     */
    public function d2s(float $d)
    {
        if ($d < 0.0) {
            return null;
        }
        $degrees = (int) $d;
        $minutes = (int) (($d - $degrees) * 60);
        $seconds = (int) (($d - $degrees - $minutes / 60) * 3600);
        return [
            $degrees . '/' . 1,
            $minutes . '/' . 1,
            ($seconds * 1000) . '/' . 1000,
        ];
    }

    /**
     * returns (latitude or longitude) coord in sexagesimal format.
     * @param   string[]   $coord
     * @param   string  $ref
     * @return  string|null
     */
    public function sexagesimal(array $coord, string $ref)
    {
        /**
         * the structure of coord must be
         * (array) [
         *      0 => (string) "ddd/d",
         *      1 => (string) "dd/d",
         *      2 => (string) "dddddd/dddd",
         * ]
         */
        if (count($coord) <> 3) {
            return null;
        }
        if (!preg_match('/^[ENSW]$/', $ref)) {
            return null;
        }
        if (
               !Exif::isRational($coord[0])
            || !Exif::isRational($coord[1])
            || !Exif::isRational($coord[2])
        ) {
            return null;
        }
        $units = $this->units[$this->lang()]['geo'];   // @phpstan-ignore-line
        $tags = [
            '{degrees:v}' => (int) Exif::rational2Float($coord[0]),
            '{minutes:v}' => (int) Exif::rational2Float($coord[1]),
            '{seconds:v}' => sprintf("%.1f", Exif::rational2Float($coord[2])),
            '{degrees:u}' => $units['degrees'], // @phpstan-ignore-line
            '{minutes:u}' => $units['minutes'], // @phpstan-ignore-line
            '{seconds:u}' => $units['seconds'], // @phpstan-ignore-line
            '{ref:u}' => $units['ref'][$ref], // @phpstan-ignore-line
        ];
        $string = $units['format']; // @phpstan-ignore-line
        foreach ($tags as $key => $value) {
            $string = str_replace($key, $value, $string); // @phpstan-ignore-line
        }
        return $string;
    }

    /**
     * returns (latitude or longitude) coord in decimal format.
     * @param   string[]    $coord
     * @param   string      $ref
     * @return  float|null
     */
    public function decimal(array $coord, string $ref)
    {
        /**
         * the structure of coord must be
         * (array) [
         *      0 => (string) "ddd/d",
         *      1 => (string) "dd/d",
         *      2 => (string) "dddddd/dddd",
         * ]
         */
        if (count($coord) <> 3) {
            return null;
        }
        foreach ($coord as $v) {
            if (!preg_match('/^\d+\/\d+$/', $v)) {
                return null;
            }
        }
        if (!preg_match('/^[ENSW]$/', $ref)) {
            return null;
        }
        return (preg_match('/^[NE]$/', $ref) ? 1 : -1) * $this->s2d($coord);
    }
}
