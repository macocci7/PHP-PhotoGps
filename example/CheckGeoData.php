<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpPhotoGps\PhotoGps;

$pg = new PhotoGps();

$images = glob('img/*.{jp*g,JP*G}', GLOB_BRACE);
sort($images);

echo "# Photo List: Geo Data\n\n";
echo "<table>\n";
echo "<tr><th>Image</th><th>Geo</th><th>Coordinate</th></tr>\n";
foreach ($images as $file) {
    $link = sprintf("<a href='%s'><img src='%s' width=100 /></a>", $file, $file);
    $pg->load($file);
    $hasGps = $pg->hasGps();
    $hasGeo = $pg->hasGeo();
    $hasAltitude = $pg->hasAltitude();
    echo sprintf("<td>%s</td>", $link);
    echo sprintf("<td>%s</td>", $hasGps ? 'o' : 'x');
    echo "<td>\n";
    if ($hasGeo) {
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
        foreach ($pg->langs() as $lang) {
            echo sprintf(
                "%s, %s<br />\n",
                $pg->lang($lang)->latitudeS(),
                $pg->lang($lang)->longitudeS()
            );
        }
    } else {
        echo "No Geo Data\n\n";
    }
    if ($hasAltitude) {
        foreach ($pg->langs() as $lang) {
            echo sprintf("%s\n\n", $pg->lang($lang)->altitudeS());
        }
    } else {
        echo "No Altitude Data\n\n";
    }
    echo "</td></tr>\n";
}
echo "</table>\n";
