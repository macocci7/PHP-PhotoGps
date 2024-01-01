<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;

$pg = new PhotoGps();

$images = glob('img/*.{jp*g,JP*G}', GLOB_BRACE);
sort($images);

echo "# Photo GPS: List\n\n";
echo "<table>\n";
echo "<tr><th>Image</th><th>GPS</th><th>Coordinate</th></tr>\n";
foreach ($images as $file) {
    $link = sprintf("<a href='%s'><img src='%s' width=100 /></a>", $file, $file);
    $pg->load($file);
    $hasGps = $pg->hasGps();
    echo sprintf("<td>%s</td>", $link);
    echo sprintf("<td>%s</td>", $hasGps ? 'o' : 'x');
    echo "<td>\n";
    if ($hasGps) {
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
                $hasGps ? $pg->lang($lang)->longitudeS() : '---',
                $hasGps ? $pg->lang($lang)->latitudeS() : '---'
            );
        }
    } else {
        echo "---\n";
    }
    echo "</td></tr>\n";
}
echo "</table>\n";
