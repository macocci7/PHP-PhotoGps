<?php

require('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$pg = new PhotoGps();
$filename = 'img/with_gps.jpg';    // includes GPS tags
$gps = $pg->coord($filename);

echo "[" . $filename . "]--------------------\n";

// Latitude in sexagesimal format
echo "Latitude: " . $pg->latitudeS($gps) . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS($gps) . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS($gps) . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS($gps) . "\n";

// Altitude
echo "Altitude: " . $pg->altitude($gps) . "\n";

// Coord in decimal format ('S' and 'W' results in negative value.)
echo "Coord: " . $pg->latitudeD($gps) . ", " . $pg->longitudeD($gps) ."\n";
