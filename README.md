# PHP-PhotoGps

`PHP-PhotoGps` is a simple library to get GPS data from a photo.

This library reads EXIF data of a jpeg file,

and can convert latitude/longitude into sexagesimal(English/Japanese) or decimal formats.

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Example](#example)
- [LICENSE](#license)

## Requirements

- PHP 8.0.0 or later
- [Image Magic PECL Extention](https://www.php.net/manual/en/book.imagick.php)
- [Composer](https://getcomposer.org/)

## Installation

```bash
composer require macocci7/php-photo-gps
```

## Usage

- PHP

    ```php
    <?php

    require('../vendor/autoload.php');

    use Macocci7\PhpPhotoGps\PhotoGps;

    $pg = new PhotoGps();
    $filename = 'img/with_gps.jpg';    // includes GPS tags
    $gps = $pg->gps($filename);

    echo "[" . $filename . "]--------------------\n";

    // Latitude in sexagesimal format
    echo "Latitude: " . $pg->latitudeS($gps) . "\n";
    echo "緯度: " . $pg->lang('ja')->latitudeS($gps) . "\n";

    // Longitude in sexagesimal format
    echo "Longitude: " . $pg->lang('eng')->longitudeS($gps) . "\n";
    echo "経度: " . $pg->lang('ja')->longitudeS($gps) . "\n";

    // Altitude
    echo "Altitude: " . $pg->altitude($gps) . "\n";

    // Coord in decimal format ('S' and 'W' results in negative value.)
    echo "Coord: " . $pg->latitudeD($gps) . ", " . $pg->longitudeD($gps) ."\n";
    ```

- OUTPUT

    ```
    [img/with_gps.jpg]--------------------
    Latitude: 37°03'26.1"N
    緯度: 37度03分26.1秒(北緯)
    Longitude: 140°53'22.4"E
    経度: 140度53分22.4秒(東経)
    Altitude: 17
    Coord: 37.057242222222, 140.889555
    ```

## Example

- [BasicUsage.php](example/BasicUsage.php) >> results in [BasicUsage.txt](example/BasicUsage.txt)

## LICENSE

[MIT](LICENSE)

***

*Document created: 2023/09/30*

*Document updated: 2023/10/17*

Copyright 2023 macocci7
