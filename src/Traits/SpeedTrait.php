<?php

namespace Macocci7\PhpPhotoGps\Traits;

use Macocci7\PhpPhotoGps\Helpers\Config;
use Macocci7\PhpPhotoGps\Helpers\Exif;

trait SpeedTrait
{
    /**
     * returns speed as float
     * @return  float|null
     */
    public function speed()
    {
        $key = 'GPSSpeed';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        return Exif::rational2Float($this->gpsData[$key]); // @phpstan-ignore-line
    }

    /**
     * returns speed as strings.
     * @return  string|null
     */
    public function speedS()
    {
        $speed = $this->speed();
        if (is_null($speed)) {
            return null;
        }
        $key = 'GPSSpeedRef';
        $ref = $this->gpsData[$key] ?? 'default';
        $units = $this->units[$this->lang()]['speed']; // @phpstan-ignore-line
        $tags = [
            '{speed:v}' => sprintf("%.2f", $speed),
            '{speed:u}' => $units['ref'][$ref], // @phpstan-ignore-line
        ];
        $string = $units['format']; // @phpstan-ignore-line
        foreach ($tags as $key => $value) {
            $string = str_replace($key, $value, $string); // @phpstan-ignore-line
        }
        return $string;
    }

    /**
     * sets format of speed, or returns current format of speed without param
     * @param   string  $format = null
     * @return  self|string
     */
    public function speedFormat(?string $format = null)
    {
        if (is_null($format)) {
            return $this->units[$this->lang()]['speed']['format']; // @phpstan-ignore-line
        }
        $this->units[$this->lang()]['speed']['format'] = $format; // @phpstan-ignore-line
        return $this;
    }

    /**
     * resets format of speed as default
     * @return  self
     */
    public function resetSpeedFormat()
    {
        $this->speedFormat(
            // @phpstan-ignore-next-line
            Config::get('units')[$this->lang()]['speed']['format']
        );
        return $this;
    }
}
