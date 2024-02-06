<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$filename = 'img/with_gps.jpg';
$pg = new PhotoGps($filename);

echo "[" . $filename . "]--------------------\n";

// Format: default
echo "Current format [eng]: " . $pg->lang('eng')->datestampFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->datestampFormat() . "\n\n";

// Datestamp: default format
echo "Datestamp [eng]: " . $pg->lang('eng')->datestamp() . "\n";
echo "Datestamp [ja]: " . $pg->lang('ja')->datestamp() . "\n\n";

// Configure Format: eng
$pg->lang('eng')->datestampFormat('l jS \of F Y');

// Current Format
echo "Current format [eng]: " . $pg->lang('eng')->datestampFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->datestampFormat() . "\n\n";

// Datestamp: Current Format
echo "Datestamp [eng]: " . $pg->lang('eng')->datestamp() . "\n";
echo "Datestamp [ja]: " . $pg->lang('ja')->datestamp() . "\n\n";

// Configure Format: ja
$pg->lang('ja')->datestampFormat('n月j日(\'y)');

// Reset Format: eng
$pg->lang('eng')->resetDatestampFormat();

// Current Format
echo "Current format [eng]: " . $pg->lang('eng')->datestampFormat() . "\n";
echo "Current format [ja]: " . $pg->lang('ja')->datestampFormat() . "\n\n";

// Datestamp: Current Format
echo "Datestamp [eng]: " . $pg->lang('eng')->datestamp() . "\n";
echo "Datestamp [ja]: " . $pg->lang('ja')->datestamp() . "\n\n";
