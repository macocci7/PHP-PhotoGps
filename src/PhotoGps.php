<?php

namespace Macocci7\PhpPhotoGps;

use Macocci7\PhpPhotoGps\Helpers\Config;
use Macocci7\PhpPhotoGps\Helpers\Exif;
use Macocci7\PhpPhotoGps\Helpers\Gps;
use Macocci7\PhpPhotoGps\Helpers\Uri;

/**
 * Gets GPS data from a photo.
 * The library only for getting GPS information from EXIF data of a jpeg file.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class PhotoGps
{
    use Traits\AttributeTrait;
    use Traits\GeoTrait;
    use Traits\DirectionTrait;
    use Traits\SpeedTrait;
    use Traits\DateTimeTrait;

    /**
     * @var string $path    path to the photo
     */
    private string $path;

    /**
     * @var mixed[]|null $gpsData GPS data
     */
    public array|null $gpsData;

    /**
     * constructor.
     * @param   string|null  $path   = null
     * @return  self
     */
    public function __construct(string|null $path = null)
    {
        $this->loadConf();
        if (!is_null($path)) {
            $this->load($path);
        }
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = ['lang', 'units', ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop); // @phpstan-ignore-line
        }
    }

    /**
     * loads GPS data from EXIF data of the photo.
     * @param   string  $path
     * @return  self
     * @thrown  \Exception
     */
    public function load(string $path)
    {
        if (!is_readable($path) && !Uri::isAvailable($path)) {
            throw new \Exception("[" . $path . "] is not readable.");
        }
        $this->path = $path;
        $this->gpsData = null;
        $this->gpsData = $this->gps();
        return $this;
    }

    /**
     * returns GPS data in the EXIF data.
     * @return mixed[]|null
     */
    public function gps()
    {
        if (!empty($this->gpsData)) {
            return $this->gpsData;
        }
        return Gps::filter(Exif::get($this->path) ?? []);
    }
}
