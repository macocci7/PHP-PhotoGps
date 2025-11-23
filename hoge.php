<?php

use Macocci7\PhpPhotoGps\PhotoGps;

$pg = new PhotoGps(__DIR__ . '/img/my_photo.jpg');

if ($pg->hasGeo()) {
    echo sprintf(
        "<a href='%s'>%s, %s</a><br />\n",
        sprintf(
            "https://www.google.com/maps/place/%s+%s/@%.7f,%.7f,17z/?authuser=0&entry=ttu",
            urlencode($pg->lang('eng')->latitudeS()),
            urlencode($pg->lang('eng')->longitudeS()),
            $pg->latitudeD(),
            $pg->longitudeD()
        ),
        sprintf("%.14f", $pg->latitudeD()),
        sprintf("%.14f", $pg->longitudeD())
    );
}
