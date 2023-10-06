<?php
namespace Macocci7\PhpPhotoGps;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Created by: macocci7
 * Date: 2023/09/30
 * Description: The library only for getting GPS information from a jpeg file.
 */

 class PhotoGps {

    /**
     * 取得対象のGPS関連EXIFタグ
     */
    private $keys = [
        'GPSLatitudeRef',   // 緯度基準（北緯 or 南緯）
        'GPSLatitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
        'GPSLongitudeRef',  // 経度基準（東経 or 西経）
        'GPSLongitude', // 経度数値（配列; 0:度/ 1:分/ 2:秒）
        'GPSAltitude',  // 高度数値（cm）
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
     * returns coordinate information in the EXIF data.
     * @param   string  $filename
     * @return  array
     */
    public function coord($filename)
    {
        if (!is_readable($filename)) return;
        $exif = $this->exif($filename);
        $gpsTags = [];
        foreach ($this->keys as $key) {
            if (array_key_exists($key, $exif))
                $gpsTags[$key] = $exif[$key];
        }
        return $gpsTags;
    }

    /**
     * returns EXIF data of the file.
     * @param   string  $filename
     * @return  array
     */
    public function exif($filename)
    {
        return Image::make($filename)->exif();
    }

    /**
     * converts GPS sexagesimal numbers to a decimal number
     * @param array $s
     * @return float
     */
    public function s2d($s)
    {
        if (!is_array($s)) return;
        if (count($s) < 3) return;
        /**
         * GPS Longitude/Latitude data structure
         * [0]: (string) "[degrees]/[scale]"
         * [1]: (string) "[minutes]/[scale]"
         * [2]: (string) "[seconds]/[scale]"
         */
        $degrees = explode("/", $s[0]);
        $minutes = explode("/", $s[1]);
        $seconds = explode("/", $s[2]);
        if (count($degrees) <> 2 | count($minutes) <> 2 | count($seconds) <> 2) return;
        return (float) (
               (int) $degrees[0] / (int) $degrees[1]
             + (int) $minutes[0] / (int) $minutes[1] / 60
             + (int) $seconds[0] / (int) $seconds[1] / 3600
            );
    }

    /**
     * converts a GPS decimal number to sexagesimal numbers
     * @param float $d
     * @return array
     */
    public function d2s($d)
    {
        if (!is_float($d)) return;
        $degrees = (int) $d;
        $minutes = (int) (($d - $degrees) * 60);
        $seconds = (int) (($d - $degrees - $minutes / 60) * 3600);
        return [
            $degrees . "/" . 1,
            $minutes . "/" . 1,
            ($seconds * 1000) . "/" . 1000,
        ];
    }
 }
