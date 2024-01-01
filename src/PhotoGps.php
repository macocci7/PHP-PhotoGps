<?php

namespace Macocci7\PhpPhotoGps;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Gets GPS data from a photo.
 * The library only for getting GPS information from EXIF data of a jpeg file.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class PhotoGps
{
    /**
     * @var string path to the photo
     */
    private string $path;

    /**
     * @var mixed[]|null $gpsData GPS data
     */
    public array|null $gpsData;

    /**
     * @var string[] EXIF GPS tags to retrieve
     */
    private array $keys;

    /**
     * @var string language: English as default
     */
    private string $lang;

    /**
     * @var mixed[] coord units in each language
     */
    private array $coordUnits;

    /**
     * constructor.
     * @param   string  $path   = null
     * @return  self
     */
    public function __construct(string $path = null)
    {
        $this->loadConf();
        //Image::configure(['driver' => 'imagick']);
        if (!is_null($path)) {
            $this->load($path);
        }
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = ['keys', 'lang', 'coordUnits', ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop);// @phpstan-ignore-line
        }
    }

    /**
     * returns config.
     * @param   string  $key = null
     * @return  mixed
     */
    public function getConf(?string $key = null)
    {
        return Config::get($key);
    }

    /**
     * returns properties.
     * @param   string  $key
     * @return  mixed
     */
    public function getProp(string $key)
    {
        if (!$this->{$key}) {
            throw new \Exception("prop $key note found.");
        }
        return $this->{$key};
    }

    /**
     * loads GPS data from EXIF data of the photo.
     * @param   string  $path
     * @return  self
     * @thrown  \Exception
     */
    public function load(string $path)
    {
        if (!is_readable($path)) {
            throw new \Exception("[" . $path . "] is not readable.");
        }
        $this->path = $path;
        $this->gpsData = $this->gps();
        return $this;
    }

    /**
     * sets specified language, returns current lang with no param.
     * @param   string  $lang = null
     * @return  self|string
     * @thrown  \Exception
     */
    public function lang(string $lang = null)
    {
        if (is_null($lang)) {
            return $this->lang;
        }
        if (!isset($this->coordUnits[$lang])) {
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
        return array_keys($this->coordUnits);
    }

    /**
     * sets format, or returns current format without param
     * @param   string  $format = null
     * @return  self|string
     */
    public function format(?string $format = null)
    {
        if (is_null($format)) {
            return $this->coordUnits[$this->lang()]['format']; // @phpstan-ignore-line
        }
        $this->coordUnits[$this->lang()]['format'] = $format; // @phpstan-ignore-line
        return $this;
    }

    /**
     * returns EXIF data of the file.
     * @return  mixed[]
     * @thrown  \Exception
     */
    public function exif()
    {
        if (!is_readable($this->path)) {
            throw new \Exception("The file is not readable.");
        }
        return Image::make($this->path)->exif(); // @phpstan-ignore-line
    }

    /**
     * returns GPS data in the EXIF data.
     * @return mixed[]
     */
    public function gps()
    {
        $gps = [];
        foreach ($this->exif() as $key => $value) {
            if (str_starts_with($key, 'GPS')) {
                $gps[$key] = $value;
            }
        }
        return $gps;
    }

    /**
     * judges if all GPS data necessary for this library exists or not.
     * @return  bool
     */
    public function hasGps()
    {
        if (!$this->gpsData) {
            return false;
        }
        foreach ($this->keys as $key) {
            if (!isset($this->gpsData[$key])) {
                return false;
            }
        }
        return true;
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
        foreach ($s as $v) {
            if (!preg_match('/^\d+\/\d+$/', $v)) {
                return null;
            }
        }
        /**
         * GPS Longitude/Latitude data structure
         * [0]: (string) "[degrees]/[scale]"
         * [1]: (string) "[minutes]/[scale]"
         * [2]: (string) "[seconds]/[scale]"
         */
        $degrees = explode('/', $s[0]);
        $minutes = explode('/', $s[1]);
        $seconds = explode('/', $s[2]);
        if (
            count($degrees) <> 2 | count($minutes) <> 2 | count($seconds) <> 2
        ) {
            return null;
        }
        return (float) (
              (int) $degrees[0] / (int) $degrees[1]
            + (int) $minutes[0] / (int) $minutes[1] / 60
            + (int) $seconds[0] / (int) $seconds[1] / 3600
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
        $degrees = explode('/', $coord[0]);
        $minutes = explode('/', $coord[1]);
        $seconds = explode('/', $coord[2]);
        if (
            count($degrees) <> 2 | count($minutes) <> 2 | count($seconds) <> 2
        ) {
            return null;
        }
        $units = $this->coordUnits[$this->lang];
        $tags = [
            '{degrees:v}' => (int) $degrees[0] / (int) $degrees[1],
            '{minutes:v}' => (int) $minutes[0] / (int) $minutes[1],
            '{seconds:v}' => sprintf("%.1f", (int) $seconds[0] / (int) $seconds[1]),
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

    /**
     * returns latitude in sexagesimal format.
     * @return  string|null
     */
    public function latitudeS()
    {
        /**
         * 'GPSLatitudeRef',   // 緯度基準（北緯 or 南緯）
         * 'GPSLatitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
         */
        if (
               !array_key_exists('GPSLatitude', $this->gpsData) // @phpstan-ignore-line
            || !array_key_exists('GPSLatitudeRef', $this->gpsData)
        ) {
            return null;
        }
        return $this->sexagesimal(
            $this->gpsData['GPSLatitude'],
            $this->gpsData['GPSLatitudeRef']
        );
    }

    /**
     * returns latitude in decimal format.
     * @return  float|null
     */
    public function latitudeD()
    {
        /**
         * 'GPSLatitudeRef',   // 緯度基準（北緯 or 南緯）
         * 'GPSLatitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
         */
        if (
               !array_key_exists('GPSLatitude', $this->gpsData) // @phpstan-ignore-line
            || !array_key_exists('GPSLatitudeRef', $this->gpsData)
        ) {
            return null;
        }
        return $this->decimal(
            $this->gpsData['GPSLatitude'],
            $this->gpsData['GPSLatitudeRef']
        );
    }

    /**
     * returns longitude in sexagesimal format.
     * @return  string|null
     */
    public function longitudeS()
    {
        /**
         * 'GPSLongitudeRef',  // 経度基準（東経 or 西経）
         * 'GPSLongitude', // 経度数値（配列; 0:度/ 1:分/ 2:秒）
         */
        if (
               !array_key_exists('GPSLongitude', $this->gpsData) // @phpstan-ignore-line
            || !array_key_exists('GPSLongitudeRef', $this->gpsData)
        ) {
            return null;
        }
        return $this->sexagesimal(
            $this->gpsData['GPSLongitude'],
            $this->gpsData['GPSLongitudeRef']
        );
    }

    /**
     * returns longitude in decimal format.
     * @return  float|null
     */
    public function longitudeD()
    {
        /**
         * 'GPSLongitudeRef',   // 緯度基準（北緯 or 南緯）
         * 'GPSLongitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
         */
        if (
               !array_key_exists('GPSLongitude', $this->gpsData) // @phpstan-ignore-line
            || !array_key_exists('GPSLongitudeRef', $this->gpsData)
        ) {
            return null;
        }
        return $this->decimal(
            $this->gpsData['GPSLongitude'],
            $this->gpsData['GPSLongitudeRef']
        );
    }

    /**
     * returns altitude
     * @return  int|null
     */
    public function altitude()
    {
        if (!array_key_exists('GPSAltitude', $this->gpsData)) { // @phpstan-ignore-line
            return null;
        }
        if (!preg_match('/^\d+\/\d+$/', $this->gpsData['GPSAltitude'])) { // @phpstan-ignore-line
            return null;
        }
        $altitudes = explode('/', $this->gpsData['GPSAltitude']); // @phpstan-ignore-line
        return (int) ( (int) $altitudes[0] / (int) $altitudes[1] );
    }
}
