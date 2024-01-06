<?php

require('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\PhotoGps;
use Macocci7\PhpPhotoGps\Dir;
use Macocci7\PhpPhotoGps\Arrow;

$pg = new PhotoGps();
$images = [
    'Remote File with GPS' => 'https://macocci7.net/photo/remote_fake_gps_001.jpg',
    'Local File with GPS' => 'img/with_gps.jpg',
    'No GPS tags' => 'img/without_gps.jpg',
];
$arrowSize = 30;

Dir::clear('./download/');
echo "# GPS Tags\n\n";

// Loop for images
foreach ($images as $title => $image) {
    echo "## $title\n\n";
    echo "<table>\n";
    $style = 'display: flex; align-items: top;';
    echo sprintf("<tr style='%s'>\n<td>\n", $style);

    // Thumbnail
    echo "<img src='$image' alt='$title' width='200'>\n\n";

    // Load GPS Data
    $pg->load($image);

    // Show Attributes Converted from GPS Data
    $style = 'display: flex; justify-content: right; align-items: center;';
    $direction = $pg->direction();
    $speedS = $pg->speedS();
    $track = $pg->track();
    $destBearing = $pg->destBearing();
    $datestamp = $pg->datestamp();
    $timestamp = $pg->timestamp();
    $judge = $direction . $speedS . $track . $destBearing . $datestamp . $timestamp;
    if (strlen($judge) > 0) {
        echo "|Attribute|Value|\n";
        echo "|:---|---:|\n";
    }

    // Image Direction
    if (!is_null($direction)) {
        $pathArrow = sprintf('img/arrow%.2f.png', $direction);
        Arrow::make($direction)->save($pathArrow);
        echo sprintf(
            "|Image Direction|<div style='%s'><img src='%s' width=%d height=%d />%s</div>|\n",
            $style,
            $pathArrow,
            $arrowSize,
            $arrowSize,
            $pg->directionS()
        );
    }

    // Speed
    if (!is_null($speedS)) {
        echo sprintf("|Speed|%s|\n", $speedS);
    }

    // Track
    if (!is_null($track)) {
        $pathArrow = sprintf('img/arrow%.2f.png', $track);
        Arrow::make($track)->save($pathArrow);
        echo sprintf(
            "|Track|<div style='%s'><img src='%s' width=%d height=%d />%s</div>|\n",
            $style,
            $pathArrow,
            $arrowSize,
            $arrowSize,
            $pg->trackS()
        );
    }

    // Dest Bearing
    if (!is_null($destBearing)) {
        $pathArrow = sprintf('img/arrow%.2f.png', $destBearing);
        Arrow::make($destBearing)->save($pathArrow);
        echo sprintf(
            "|Destination Bearing|<div style='%s'><img src='%s' width=%d height=%d />%s</div>|\n",
            $style,
            $pathArrow,
            $arrowSize,
            $arrowSize,
            $pg->destBearingS()
        );
    }

    // Date Stamp
    echo $datestamp ? sprintf("|Datestamp|%s (UTC)|\n", $datestamp) : '';

    // Time Stamp
    echo $timestamp ? sprintf("|Timestamp|%s (UTC)|\n", $timestamp) : '';

    echo "</td>\n<td>\n\n";

    // Show GPS Data
    if (!$pg->hasGps()) {
        echo "No GPS data.\n\n";
        continue;
    }
    echo "|Tag|Value|\n";
    echo "|:---|---:|\n";
    foreach ($pg->gps() as $tag => $value) {
        echo sprintf(
            "|%s|%s|\n",
            $tag,
            is_array($value) ? implode('<br />', $value) : $value
        );
    }

    echo "</td>\n</tr>\n</table>\n\n";
}
