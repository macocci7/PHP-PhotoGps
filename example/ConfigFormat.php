<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$filename = 'img/with_gps.jpg';    // includes GPS tags
$pg = new PhotoGps($filename);

echo "[" . $filename . "]--------------------\n";

// Format: default

// Latitude in sexagesimal format
echo "Latitude: " . $pg->latitudeS() . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS() . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS() . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS() . "\n";

echo "--------------------------------------\n";

// Configure format
$pg->lang('eng')->format('{ref:u}: {seconds:v}{seconds:u}, {minutes:v}{minutes:u}, {degrees:v}{degrees:u}');

// Curret format
echo "Current format [eng]: " . $pg->lang('eng')->format() . "\n";

// Latitude in sexagesimal format
echo "Latitude: " . $pg->latitudeS() . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS() . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS() . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS() . "\n";

echo "--------------------------------------\n";

// Configure format
$pg->lang('ja')->format('{seconds:v}{seconds:u}, {minutes:v}{minutes:u}, {degrees:v}{degrees:u} ({ref:u})');

// Curret format
echo "Current format [ja]: " . $pg->lang('eng')->format() . "\n";

// Latitude in sexagesimal format
echo "Latitude: " . $pg->latitudeS() . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS() . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS() . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS() . "\n";
