<?php

require('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$pg = new PhotoGps();
$files = [
    'img/with_gps.jpg',    // GPSタグ有り
    'img/without_gps.jpg', // GPSタグ無し
    'img/without_gps.png', // PNG
    'img/not_found.jpg', // 存在しないファイル
];

foreach ($files as $filename) {
    echo "[" . $filename . "]--------------------\n";
    var_dump($pg->exif($filename));
}
