<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpPhotoGps\Helper\Gps;

echo "# Exif: GPS Attribute Information\n\n";
echo "|Field Name|Type|Count|Values|Default|Separator|\n";
echo "|:---|:---|---:|:---|:---:|:---:|\n";
foreach (Gps::def() as $key => $value) {
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
