<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$filename = 'img/with_gps.jpg';
$pg = new PhotoGps($filename);

echo "[" . $filename . "]--------------------\n";

// Format: default
echo "Current format [eng]: " . $pg->lang('eng')->timestampFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->timestampFormat() . "\n\n";

// Timestamp: default format
echo "Timestamp [eng]: " . $pg->lang('eng')->timestamp() . "\n";
echo "Timestamp [ja]: " . $pg->lang('ja')->timestamp() . "\n\n";

// Configure Format: eng
$pg->lang('eng')->timestampFormat('g:i a');

// Current Format
echo "Current format [eng]: " . $pg->lang('eng')->timestampFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->timestampFormat() . "\n\n";

// Timestamp: Current Format
echo "Timestamp [eng]: " . $pg->lang('eng')->timestamp() . "\n";
echo "Timestamp [ja]: " . $pg->lang('ja')->timestamp() . "\n\n";

// Configure Format: ja
$pg->lang('ja')->timestampFormat('G時i分s秒');

// Reset Format: eng
$pg->lang('eng')->resetTimestampFormat();

// Current Format
echo "Current format [eng]: " . $pg->lang('eng')->timestampFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->timestampFormat() . "\n\n";

// Timestamp: Current Format
echo "Timestamp [eng]: " . $pg->lang('eng')->timestamp() . "\n";
echo "Timestamp [ja]: " . $pg->lang('ja')->timestamp() . "\n\n";
