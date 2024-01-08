<?php

namespace Macocci7\PhpPhotoGps\Helper;

use Intervention\Image\ImageManagerStatic as Image;
use Macocci7\PhpPhotoGps\Helper\Config;
use Macocci7\PhpPhotoGps\Helper\Exif;

/**
 * Class for Handling an Arrow Image
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Arrow
{
    /**
     * @var \Intervention\Image\Image   $image
     */
    private $image;

    /**
     * constructor.
     * @param   float   $degrees
     */
    public function __construct(float $degrees)
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
        $basePath = __DIR__ . '/' . Config::get('pathBaseArrow');
        $this->image = Image::make($basePath);
        if ($degrees > 0) {
            $this->image->rotate(0 - $degrees);
        }
    }

    /**
     * makes compass image rotated.
     * @param   float   $degree
     * @return  \Macocci7\PhpPhotoGps\Helper\Arrow
     */
    public static function make(float $degree)
    {
        return new Arrow($degree);
    }

    /**
     * saves image into a file.
     * @param   string  $path
     * @return  self
     * @thrown  \Exception
     */
    public function save(string $path)
    {
        $this->image->save($path);
        return $this;
    }
}
