<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpPhotoGps\PhotoGps;

$filename = 'img/with_gps.jpg';    // includes GPS tags
$pg = new PhotoGps($filename);

echo "[" . $filename . "]--------------------\n";

// Format: default
echo "Current format [eng]: " . $pg->lang('eng')->format() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->format() . "\n\n";

// Latitude in sexagesimal format
echo "Latitude: " . $pg->lang('eng')->latitudeS() . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS() . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS() . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS() . "\n\n";

echo "[Chang format:eng]---------------------------------\n";

// Configure format
$pg->lang('eng')->format('{ref:u}: {seconds:v}{seconds:u}, {minutes:v}{minutes:u}, {degrees:v}{degrees:u}');

// Current format
echo "Current format [eng]: " . $pg->lang('eng')->format() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->format() . "\n\n";

// Latitude in sexagesimal format
echo "Latitude: " . $pg->lang('eng')->latitudeS() . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS() . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS() . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS() . "\n\n";

echo "[Change format:ja]---------------------------------\n";

// Configure format
$pg->lang('ja')->format('{seconds:v}{seconds:u}, {minutes:v}{minutes:u}, {degrees:v}{degrees:u} ({ref:u})');

// Current format
echo "Current format [eng]: " . $pg->lang('eng')->format() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->format() . "\n\n";

// Latitude in sexagesimal format
echo "Latitude: " . $pg->lang('eng')->latitudeS() . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS() . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS() . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS() . "\n\n";

echo "[Reset format:ja]---------------------------------\n";

// Reset format
$pg->lang('ja')->resetFormat();

// Current format
echo "Current format [eng]: " . $pg->lang('eng')->format() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->format() . "\n\n";

// Latitude in sexagesimal format
echo "Latitude: " . $pg->lang('eng')->latitudeS() . "\n";
echo "緯度: " . $pg->lang('ja')->latitudeS() . "\n";

// Longitude in sexagesimal format
echo "Longitude: " . $pg->lang('eng')->longitudeS() . "\n";
echo "経度: " . $pg->lang('ja')->longitudeS() . "\n\n";
