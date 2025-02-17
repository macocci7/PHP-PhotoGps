<?php

namespace Macocci7\PhpPhotoGps\Helpers;

use Intervention\Image\ImageManager as Image;
use Macocci7\PhpPhotoGps\Helpers\Config;
use Macocci7\PhpPhotoGps\Helpers\Exif;

/**
 * Class for Handling an Arrow Image
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Arrow
{
    /**
     * constructor.
     */
    private function __construct()
    {
    }

    /**
     * makes compass image rotated.
     * @param   float   $degrees
     * @return  \Intervention\Image\Interfaces\ImageInterface
     */
    public static function make(float $degrees)
    {
        Config::load();
        // t = 360 - deg
        // | degree | direction |
        // |  ---   |    ---    |
        // |    0°  |   North   |
        // |   90°  |   East    |
        // |  180°  |   South   |
        // |  270°  |   West    |
        // |  360°  |   North   |
        $degrees = Exif::simplifyDegrees($degrees);
        $basePath = __DIR__ . '/' . Config::get('pathBaseArrow');   // @phpstan-ignore-line
        $image = Image::gd()->read($basePath);
        if ($degrees > 0) {
            $image->rotate(0 - $degrees, 'transparent');
        }
        return $image;
    }
}
