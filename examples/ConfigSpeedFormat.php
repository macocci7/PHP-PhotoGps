<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpPhotoGps\PhotoGps;

$filename = 'img/with_gps.jpg';
$pg = new PhotoGps($filename);

echo "[" . $filename . "]--------------------\n";

// Format: default
echo "Current format [eng]: " . $pg->lang('eng')->speedFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->speedFormat() . "\n\n";

// Speed: default format
echo "Speed [eng]: " . $pg->lang('eng')->speedS() . "\n";
echo "Speed [ja]: " . $pg->lang('ja')->speedS() . "\n\n";

// Configure Format: eng
$pg->lang('eng')->speedFormat('{speed:v}({speed:u})');

// Current Format
echo "Current format [eng]: " . $pg->lang('eng')->speedFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->speedFormat() . "\n\n";

// Speed: Current Format
echo "Speed [eng]: " . $pg->lang('eng')->speedS() . "\n";
echo "Speed [ja]: " . $pg->lang('ja')->speedS() . "\n\n";

// Configure Format: ja
$pg->lang('ja')->speedFormat('時速{speed:v}マイル');

// Reset Format: eng
$pg->lang('eng')->resetSpeedFormat();

// Current Format
echo "Current format [eng]: " . $pg->lang('eng')->speedFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->speedFormat() . "\n\n";

// Speed: Current Format
echo "Speed [eng]: " . $pg->lang('eng')->speedS() . "\n";
echo "Speed [ja]: " . $pg->lang('ja')->speedS() . "\n\n";
