<?php

namespace Macocci7\PhpPhotoGps;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Created by: macocci7
 * Date: 2023/09/30
 * Description: The library only for getting GPS information from a jpeg file.
 */

class PhotoGps
{
    /**
     * 取得対象のGPS関連EXIFタグ
     */
    private $keys = [
        'GPSLatitudeRef',   // 緯度基準（北緯 or 南緯）
        'GPSLatitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
        'GPSLongitudeRef',  // 経度基準（東経 or 西経）
        'GPSLongitude', // 経度数値（配列; 0:度/ 1:分/ 2:秒）
        'GPSAltitude',  // 高度数値（m）
    ];
    private $lang = 'eng';  // English as default
    private $coordUnits = [
        'eng' => [
            'degrees' => '°',
            'minutes' => "'",
            'seconds' => '"',
            'ref' => [
                'N' => 'N',
                'S' => 'S',
                'E' => 'E',
                'W' => 'W',
            ],
        ],
        'ja' => [
            'degrees' => '度',
            'minutes' => '分',
            'seconds' => '秒',
            'ref' => [
                'N' => '(北緯)',
                'S' => '(南緯)',
                'E' => '(東経)',
                'W' => '(西経)',
            ],
        ],
    ];

    /**
     * constructor
     * @param
     * @return
     */
    public function __construct()
    {
        Image::configure(['driver' => 'imagick']);
    }

    /**
     * sets specified language
     * @param   string  $lang
     * @return  self
     */
    public function lang(string $lang = null)
    {
        if (is_null($lang)) {
            return $this->lang;
        }
        if (!isset($this->coordUnits[$lang])) {
            return;
        }
        $this->lang = $lang;
        return $this;
    }

    /**
     * returns supported langages
     * @param
     * @return  array
     */
    public function langs()
    {
        return array_keys($this->coordUnits);
    }

    /**
     * returns EXIF data of the file.
     * @param   string  $filename
     * @return  array
     */
    public function exif(string $filename)
    {
        if (!is_readable($filename)) {
            return;
        }
        return Image::make($filename)->exif();
    }

    /**
     * returns GPS data in the EXIF data.
     * @param   string  $filename
     * @return array
     */
    public function gps(string $filename)
    {
        $exif = $this->exif($filename);
        if (!$exif) {
            return;
        }
        $gps = [];
        foreach ($exif as $key => $value) {
            if (preg_match('/^GPS/', $key)) {
                $gps[$key] = $value;
            }
        }
        return $gps;
    }

    /**
     * converts GPS sexagesimal numbers into a decimal number
     * @param array $s
     * @return float
     */
    public function s2d(array $s)
    {
        if (count($s) < 3) {
            return;
        }
        foreach ($s as $v) {
            if (!preg_match('/^\d+\/\d+$/', $v)) {
                return;
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
            return;
        }
        return (float) (
              (int) $degrees[0] / (int) $degrees[1]
            + (int) $minutes[0] / (int) $minutes[1] / 60
            + (int) $seconds[0] / (int) $seconds[1] / 3600
        );
    }

    /**
     * converts a GPS decimal number into sexagesimal numbers
     * @param float $d
     * @return array
     */
    public function d2s(float $d)
    {
        if ($d < 0.0) {
            return;
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
     * @param   array   $coord
     * @param   string  $ref
     * @return  string
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
            return;
        }
        if (!preg_match('/^[ENSW]$/', $ref)) {
            return;
        }
        $degrees = explode('/', $coord[0]);
        $minutes = explode('/', $coord[1]);
        $seconds = explode('/', $coord[2]);
        if (
            count($degrees) <> 2 | count($minutes) <> 2 | count($seconds) <> 2
        ) {
            return;
        }
        $units = $this->coordUnits[$this->lang];
        return sprintf(
            "%d" . $units['degrees'] . "%02d" . $units['minutes'] . "%0.1f" . $units['seconds'] . "%s",
            (int) $degrees[0] / (int) $degrees[1],
            (int) $minutes[0] / (int) $minutes[1],
            (int) $seconds[0] / (int) $seconds[1],
            $units['ref'][$ref]
        );
    }

    /**
     * returns (latitude or longitude) coord in decimal format.
     * @param   array   $coord
     * @param   string  $ref
     * @return  string
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
            return;
        }
        foreach ($coord as $v) {
            if (!preg_match('/^\d+\/\d+$/', $v)) {
                return;
            }
        }
        if (!preg_match('/^[ENSW]$/', $ref)) {
            return;
        }
        return (preg_match('/^[NE]$/', $ref) ? 1 : -1) * $this->s2d($coord);
    }

    /**
     * returns latitude in sexagesimal format.
     * @param   array   $gps
     * @return  string
     */
    public function latitudeS(array $gps)
    {
        /**
         * 'GPSLatitudeRef',   // 緯度基準（北緯 or 南緯）
         * 'GPSLatitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
         */
        if (
              !array_key_exists('GPSLatitude', $gps)
            | !array_key_exists('GPSLatitudeRef', $gps)
        ) {
            return;
        }
        return $this->sexagesimal($gps['GPSLatitude'], $gps['GPSLatitudeRef']);
    }

    /**
     * returns latitude in decimal format.
     * @param   array   $gps
     * @return  float
     */
    public function latitudeD(array $gps)
    {
        /**
         * 'GPSLatitudeRef',   // 緯度基準（北緯 or 南緯）
         * 'GPSLatitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
         */
        if (
              !array_key_exists('GPSLatitude', $gps)
            | !array_key_exists('GPSLatitudeRef', $gps)
        ) {
            return;
        }
        return $this->decimal($gps['GPSLatitude'], $gps['GPSLatitudeRef']);
    }

    /**
     * returns longitude in sexagesimal format.
     * @param   array   $gps
     * @return  string
     */
    public function longitudeS(array $gps)
    {
        /**
         * 'GPSLongitudeRef',  // 経度基準（東経 or 西経）
         * 'GPSLongitude', // 経度数値（配列; 0:度/ 1:分/ 2:秒）
         */
        if (
              !array_key_exists('GPSLongitude', $gps)
            | !array_key_exists('GPSLongitudeRef', $gps)
        ) {
            return;
        }
        return $this->sexagesimal(
            $gps['GPSLongitude'],
            $gps['GPSLongitudeRef']
        );
    }

    /**
     * returns longitude in decimal format.
     * @param   array   $gps
     * @return  float
     */
    public function longitudeD(array $gps)
    {
        /**
         * 'GPSLongitudeRef',   // 緯度基準（北緯 or 南緯）
         * 'GPSLongitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
         */
        if (
              !array_key_exists('GPSLongitude', $gps)
            | !array_key_exists('GPSLongitudeRef', $gps)
        ) {
            return;
        }
        return $this->decimal($gps['GPSLongitude'], $gps['GPSLongitudeRef']);
    }

    /**
     * returns altitude
     * @param   array   $gps
     * @return  integer
     */
    public function altitude(array $gps)
    {
        if (!array_key_exists('GPSAltitude', $gps)) {
            return;
        }
        if (!preg_match('/^\d+\/\d+$/', $gps['GPSAltitude'])) {
            return;
        }
        $altitudes = explode('/', $gps['GPSAltitude']);
        return (int) ( (int) $altitudes[0] / (int) $altitudes[1] );
    }
}
