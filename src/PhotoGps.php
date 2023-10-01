<?php
namespace Macocci7\PhpPhotoGps;

require('../vendor/autoload.php');

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
 }
