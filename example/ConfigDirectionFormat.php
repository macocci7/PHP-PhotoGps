<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$filename = 'img/with_gps.jpg';
$pg = new PhotoGps($filename);

echo "[" . $filename . "]--------------------\n";

// Format: default
echo "Current format [eng]: " . $pg->lang('eng')->directionFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->directionFormat() . "\n\n";

// Image Direction: default format
echo "Image Direction [eng]: " . $pg->lang('eng')->directionS() . "\n";
echo "Image Direction [ja]: " . $pg->lang('ja')->directionS() . "\n\n";

// Configure Format: eng
$pg->lang('eng')->directionFormat('{degrees:v}{degrees:u}({ref})');

// Current Format
echo "Current format [eng]: " . $pg->lang('eng')->directionFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->directionFormat() . "\n\n";

// Image Direction: Current Format
echo "Image Direction [eng]: " . $pg->lang('eng')->directionS() . "\n";
echo "Image Direction [ja]: " . $pg->lang('ja')->directionS() . "\n\n";

// Configure Format: ja
$pg->lang('ja')->directionFormat('{degrees:v}{degrees:u}');

// Reset Format: eng
$pg->lang('eng')->resetDirectionFormat();

// Current Format
echo "Current format [eng]: " . $pg->lang('eng')->directionFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->directionFormat() . "\n\n";

// Image Direction: Current Format
echo "Image Direction [eng]: " . $pg->lang('eng')->directionS() . "\n";
echo "Image Direction [ja]: " . $pg->lang('ja')->directionS() . "\n\n";
