<?php

namespace Macocci7\PhpPhotoGps\Traits;

use Macocci7\PhpPhotoGps\Helpers\Config;
use Macocci7\PhpPhotoGps\Helpers\Exif;

trait GeoTrait
{
    /**
     * judges if any GPS Geo data exists or not.
     * GPS Geo data means: longitude and latitude.
     * Their tag names must be specified in 'conf/PhotoGps.neon'.
     * @return  bool
     */
    public function hasGeo()
    {
        foreach (Config::get('geo') as $key) { // @phpstan-ignore-line
            if (isset($this->gpsData[$key])) { // @phpstan-ignore-line
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
            if (!isset($this->gpsData[$key])) { // @phpstan-ignore-line
                return false;
            }
        }
        return true;
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
        $keyV = 'GPSAltitude';
        if (!isset($this->gpsData[$keyV])) {
            return null;
        }
        $val = $this->gpsData[$keyV];
        if (!Exif::isRational($val)) { // @phpstan-ignore-line
            return null;
        }
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
        $preKey = sprintf(
            "units.%s.altitudeRef.exif%s.%s",
            $this->lang(), // @phpstan-ignore-line
            Exif::version(),
            $ref ?? 'default' // @phpstan-ignore-line
        );
        $pre = Config::get($preKey);
        // @phpstan-ignore-next-line
        $unit = Config::get('units')[$this->lang()]['altitude'];
        return sprintf(
            "%s %.2f %s",
            $pre, // @phpstan-ignore-line
            $altitude,
            $unit // @phpstan-ignore-line
        );
    }
}
