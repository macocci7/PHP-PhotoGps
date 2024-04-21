<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpPhotoGps\Helpers\Gps;

//$exifVersion = "0210";
//$exifVersion = "0220";
//$exifVersion = "0221";
//$exifVersion = "0230";
//$exifVersion = "0231";
//$exifVersion = "0232";
$exifVersion = "0300";
echo "# Exif" . $exifVersion . ": GPS Attribute Information\n\n";
echo "|Field Name|Type|Count|Values|Default|Separator|\n";
echo "|:---|:---|---:|:---|:---:|:---:|\n";
foreach (Gps::def('exif' . $exifVersion . '.fields') as $key => $value) {
    echo sprintf(
        "|%s|%s|%d|%s|%s|%s|\n",
        $key,
        $value['type'],
        $value['count'],
        isset($value['values'])
        ? implode(
            '<br />',
            array_map(
                fn ($k, $v) => '* ' . $k . ': ' . $v,
                array_keys($value['values']),
                $value['values']
            )
        )
        : '---',
        $value['default'] ?? '---',
        $value['separator'] ?? '---'
    );
}
