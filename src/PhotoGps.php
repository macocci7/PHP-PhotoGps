<?php

namespace Macocci7\PhpPhotoGps;

use Intervention\Image\ImageManagerStatic as Image;
use Macocci7\PhpPhotoGps\Helper\Config;
use Macocci7\PhpPhotoGps\Helper\Uri;
use Macocci7\PhpPhotoGps\Helper\Exif;
use Macocci7\PhpPhotoGps\Helper\Gps;
use Macocci7\PhpPhotoGps\Helper\File;

/**
 * Gets GPS data from a photo.
 * The library only for getting GPS information from EXIF data of a jpeg file.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class PhotoGps
{
    /**
     * @var string $path    path to the photo
     */
    private string $path;

    /**
     * @var mixed[]|null $gpsData GPS data
     */
    public array|null $gpsData;

    /**
     * @var string  $lang
     */
    private string $lang;

    /**
     * @var mixed[]     $units
     */
    private array $units;

    /**
     * constructor.
     * @param   string  $path   = null
     * @return  self
     */
    public function __construct(string $path = null)
    {
        $this->loadConf();
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
        $props = ['lang', 'units', ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop); // @phpstan-ignore-line
        }
    }

    /**
     * loads GPS data from EXIF data of the photo.
     * @param   string  $path
     * @return  self
     * @thrown  \Exception
     */
    public function load(string $path)
    {
        if (!is_readable($path) && !Uri::isAvailable($path)) {
            throw new \Exception("[" . $path . "] is not readable.");
        }
        $this->path = $path;
        $this->gpsData = null;
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
            return $this->units[$this->lang()]['format']; // @phpstan-ignore-line
        }
        $this->units[$this->lang()]['format'] = $format; // @phpstan-ignore-line
        return $this;
    }

    /**
     * resets format as default
     * @return  self
     */
    public function resetFormat()
    {
        // @phpstan-ignore-next-line
        $this->format(Config::get('units')[$this->lang()]['format']);
        return $this;
    }

    /**
     * returns EXIF data of the file.
     * @return  mixed[]|null
     * @thrown  \Exception
     */
    public function exif()
    {
        $path = $this->path;
        if (!is_readable($path) && !Uri::isAvailable($path)) {
            throw new \Exception("The file is not readable.");
        }
        if (Uri::isAvailable($path)) {
            if (!ini_get('allow_url_fopen')) {
                ini_set('allow_url_fopen', '1');
            }
            $path = File::download($this->path);
            if (!$path) {
                return null;
            }
        }
        return Image::make($path)->exif(); // @phpstan-ignore-line
    }

    /**
     * returns GPS data in the EXIF data.
     * @return mixed[]|null
     */
    public function gps()
    {
        if (is_array($this->gpsData)) {
            if (!empty($this->gpsData)) {
                return $this->gpsData;
            }
        }
        return Gps::filter($this->exif());  // @phpstan-ignore-line
    }

    /**
     * judges if any GPS Geo data exists or not.
     * GPS Geo data means: longitude and latitude.
     * Their tag names must be specified in 'conf/PhotoGps.neon'.
     * @return  bool
     */
    public function hasGeo()
    {
        foreach (Config::get('geo') as $key) { // @phpstan-ignore-line
            if (isset($this->gpsData[$key])) {
                return true;
            }
        }
        return false;
    }

    /**
     * judges if any GPS data exist or not.
     * @return  bool
     */
    public function hasGps()
    {
        if (empty($this->gpsData)) {
            return false;
        }
        return true;
    }

    /**
     * judges if all altitude data exists or not.
     * @return  bool
     */
    public function hasAltitude()
    {
        foreach (Config::get('altitude') as $key) { // @phpstan-ignore-line
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
        $units = $this->units[$this->lang()];   // @phpstan-ignore-line
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
               !isset($this->gpsData['GPSLatitude'])
            || !isset($this->gpsData['GPSLatitudeRef'])
        ) {
            return null;
        }
        return $this->sexagesimal(
            $this->gpsData['GPSLatitude'], // @phpstan-ignore-line
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
               !isset($this->gpsData['GPSLatitude'])
            || !isset($this->gpsData['GPSLatitudeRef'])
        ) {
            return null;
        }
        return $this->decimal(
            $this->gpsData['GPSLatitude'], // @phpstan-ignore-line
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
               !isset($this->gpsData['GPSLongitude'])
            || !isset($this->gpsData['GPSLongitudeRef'])
        ) {
            return null;
        }
        return $this->sexagesimal(
            $this->gpsData['GPSLongitude'], // @phpstan-ignore-line
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
               !isset($this->gpsData['GPSLongitude'])
            || !isset($this->gpsData['GPSLongitudeRef'])
        ) {
            return null;
        }
        return $this->decimal(
            $this->gpsData['GPSLongitude'], // @phpstan-ignore-line
            $this->gpsData['GPSLongitudeRef']
        );
    }

    /**
     * returns altitude
     * @return  float|null
     */
    public function altitude()
    {
        $keyR = 'GPSAltitudeRef';
        $keyV = 'GPSAltitude';
        if (!isset($this->gpsData[$keyV])) {
            return null;
        }
        $ref = $this->gpsData[$keyR] ?? null;
        $val = $this->gpsData[$keyV];
        if (!Exif::isRational($val)) { // @phpstan-ignore-line
            return null;
        }
        $ref = (int) $ref;
        return Exif::rational2Float($val); // @phpstan-ignore-line
    }

    /**
     * returns formatted altitude.
     * @return  string|null
     */
    public function altitudeS()
    {
        $altitude = $this->altitude();
        if (is_null($altitude)) {
            return null;
        }
        $ref = $this->gpsData['GPSAltitudeRef'] ?? null;
        // @phpstan-ignore-next-line
        $pre = Config::get('units')[$this->lang()]['altitudeRef'][$ref ?? 'default'] ?? null;
        // @phpstan-ignore-next-line
        $unit = Config::get('units')[$this->lang()]['altitude'];
        return sprintf(
            "%s %.2f %s",
            $pre, // @phpstan-ignore-line
            $altitude,
            $unit // @phpstan-ignore-line
        );
    }

    /**
     * returns direction as degree.
     * @return  int|float|null
     */
    public function direction()
    {
        $key = 'GPSImgDirection';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $degrees = Exif::rational2Float($this->gpsData[$key]); // @phpstan-ignore-line
        return Exif::simplifyDegrees($degrees); // @phpstan-ignore-line
    }

    /**
     * returns image direction as strings.
     * @return  string|null
     */
    public function directionS()
    {
        $degrees = $this->direction();
        if (is_null($degrees)) {
            return null;
        }
        $ref = '';
        $key = 'GPSImgDirectionRef';
        if (isset($this->gpsData[$key])) {
            $ref = $this->gpsData[$key];
        }
        return sprintf("%s %.2f°", $ref, $degrees); // @phpstan-ignore-line
    }

    /**
     * returns speed as float
     * @return  float|null
     */
    public function speed()
    {
        $key = 'GPSSpeed';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        return Exif::rational2Float($this->gpsData[$key]); // @phpstan-ignore-line
    }

    /**
     * returns speed as strings.
     * @return  string|null
     */
    public function speedS()
    {
        $speed = $this->speed();
        if (is_null($speed)) {
            return null;
        }
        $key = 'GPSSpeedRef';
        $ref = $this->gpsData[$key] ?? 'default';
        $unit = Config::get('units')[$this->lang()]['speed'][$ref]; // @phpstan-ignore-line
        return sprintf("%.2f%s", $speed, $unit); // @phpstan-ignore-line
    }

    /**
     * returns destination bearing as degree.
     * @return  int|float|null
     */
    public function destBearing()
    {
        $key = 'GPSDestBearing';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $degrees = Exif::rational2Float($this->gpsData[$key]); // @phpstan-ignore-line
        return Exif::simplifyDegrees($degrees); // @phpstan-ignore-line
    }

    /**
     * returns destination bearing as strings.
     * @return  string|null
     */
    public function destBearingS()
    {
        $degrees = $this->destBearing();
        if (is_null($degrees)) {
            return null;
        }
        $key = 'GPSDestBearingRef';
        $ref = $this->gpsData[$key] ?? null;
        return sprintf("%s %.2f°", $ref, $degrees); // @phpstan-ignore-line
    }

    /**
     * returns track as degree.
     * @return  int|float|null
     */
    public function track()
    {
        $key = 'GPSTrack';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $degrees = Exif::rational2Float($this->gpsData[$key]); // @phpstan-ignore-line
        return Exif::simplifyDegrees($degrees); // @phpstan-ignore-line
    }

    /**
     * returns track as strings.
     * @return  string|null
     */
    public function trackS()
    {
        $degrees = $this->track();
        if (is_null($degrees)) {
            return null;
        }
        $key = 'GPSTrackRef';
        $ref = $this->gpsData[$key] ?? null;
        return sprintf("%s %.2f°", $ref, $degrees); // @phpstan-ignore-line
    }

    /**
     * returns converted date stamp
     * @return  string|null
     */
    public function datestamp()
    {
        $key = 'GPSDateStamp';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $items = explode(':', $this->gpsData[$key]); // @phpstan-ignore-line
        return implode('/', $items);
    }

    /**
     * returns converted time stamp
     * @return  string|null
     */
    public function timestamp()
    {
        $key = 'GPSTimeStamp';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $timestamp = $this->gpsData[$key];
        return implode(
            ':',
            array_map(
                fn ($v) => sprintf("%02d", Exif::rational2Float($v)), // @phpstan-ignore-line
                $timestamp // @phpstan-ignore-line
            )
        );
    }
}
