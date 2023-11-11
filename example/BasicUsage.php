<?php

require('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$filename = 'img/with_gps.jpg';    // includes GPS tags
$pg = new PhotoGps($filename);

echo "[" . $filename . "]--------------------\n";

// Latitude in sexagesimal format
echo "Latitude: " . $pg->latitudeS() . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS() . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS() . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS() . "\n";

// Altitude
echo "Altitude: " . $pg->altitude() . "\n";

// Coord in decimal format ('S' and 'W' results in negative value.)
echo "Coord: " . $pg->latitudeD() . ", " . $pg->longitudeD() . "\n";
