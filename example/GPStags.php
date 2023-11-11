<?php

require('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$pg = new PhotoGps();
$files = [
    'img/with_gps.jpg',    // GPS tags included
    'img/without_gps.jpg', // GPS tags not included
];

foreach ($files as $filename) {
    echo "[" . $filename . "]--------------------\n";
    $pg->load($filename);
    var_dump($pg->gps($filename));
}
