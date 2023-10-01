<?php

require('../src/PhotoGps.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$pg = new PhotoGps();
$files = [
    'img/latov.jpg',    // GPSタグ有り
    'img/IMG_1119.jpg', // GPSタグ無し
    'img/not_found.jpg', // 存在しないファイル
];

foreach ($files as $filename) {
    echo "[" . $filename . "]--------------------\n";
    var_dump($pg->coord($filename));
}
