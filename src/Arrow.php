<?php

namespace Macocci7\PhpPhotoGps;

use Intervention\Image\ImageManagerStatic as Image;
use Macocci7\PhpPhotoGps\Gps;
use Macocci7\PhpPhotoGps\Exif;

/**
 * Class for Handling an Arrow Image
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Arrow
{
    /**
     * @var string  $imageArrow
     */
    private string $pathBaseArrow = __DIR__ . '/img/arrow_red_50x50.png';
    private $image;

    public function __construct(float $degrees)
    {
        // t = 360 - deg
        // | degree | direction |
        // |  ---   |    ---    |
        // |    0°  |   North   |
        // |   90°  |   East    |
        // |  180°  |   South   |
        // |  270°  |   West    |
        // |  360°  |   North   |
        $degrees = Exif::simplifyDegrees($degrees);
        $this->image = Image::make($this->pathBaseArrow);
        if ($degrees > 0) {
            $this->image->rotate(0 - $degrees);
        }
    }

    /**
     * makes compass image rotated.
     * @param   float   $degree
     * @return  Macocci7\PhpPhotoGps\Compass
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
