<?php

namespace Macocci7\PhpPhotoGps\Traits;

use Macocci7\PhpPhotoGps\Helpers\Config;
use Macocci7\PhpPhotoGps\Helpers\Exif;

trait DirectionTrait
{
    /**
     * returns direction as degree.
     * @return  int|float|null
     */
    public function direction()
    {
        $key = 'GPSImgDirection';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $degrees = Exif::rational2Float($this->gpsData[$key]); // @phpstan-ignore-line
        return Exif::simplifyDegrees($degrees); // @phpstan-ignore-line
    }

    /**
     * returns image direction as strings.
     * @return  string|null
     */
    public function directionS()
    {
        $degrees = $this->direction();
        if (is_null($degrees)) {
            return null;
        }
        $key = 'GPSImgDirectionRef';
        return $this->formattedDirection($key, $degrees);
    }

    /**
     * returns formatted direction
     * @param   string  $key
     * @param   float   $degrees
     * @return  string
     */
    private function formattedDirection(string $key, float $degrees)
    {
        $ref = '';
        $units = $this->units[$this->lang()]['direction']; // @phpstan-ignore-line
        if (isset($this->gpsData[$key])) {
            $ref = $units['ref'][$this->gpsData[$key]]; // @phpstan-ignore-line
        }
        $tags = [
            '{ref}' => $ref,
            '{degrees:v}' => sprintf("%.2f", $degrees),
            '{degrees:u}' => $units['degrees'], // @phpstan-ignore-line
        ];
        $string = $units['format']; // @phpstan-ignore-line
        foreach ($tags as $key => $value) {
            $string = str_replace($key, $value, $string); // @phpstan-ignore-line
        }
        return $string;
    }


    /**
     * sets format of direction, or returns current format of direction without param
     * @param   string  $format = null
     * @return  self|string
     */
    public function directionFormat(?string $format = null)
    {
        if (is_null($format)) {
            return $this->units[$this->lang()]['direction']['format']; // @phpstan-ignore-line
        }
        $this->units[$this->lang()]['direction']['format'] = $format; // @phpstan-ignore-line
        return $this;
    }

    /**
     * resets format of direction as default
     * @return  self
     */
    public function resetDirectionFormat()
    {
        $this->directionFormat(
            // @phpstan-ignore-next-line
            Config::get('units')[$this->lang()]['direction']['format']
        );
        return $this;
    }

    /**
     * returns destination bearing as degree.
     * @return  int|float|null
     */
    public function destBearing()
    {
        $key = 'GPSDestBearing';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $degrees = Exif::rational2Float($this->gpsData[$key]); // @phpstan-ignore-line
        return Exif::simplifyDegrees($degrees); // @phpstan-ignore-line
    }

    /**
     * returns destination bearing as strings.
     * @return  string|null
     */
    public function destBearingS()
    {
        $degrees = $this->destBearing();
        if (is_null($degrees)) {
            return null;
        }
        $key = 'GPSDestBearingRef';
        return $this->formattedDirection($key, $degrees);
    }

    /**
     * returns track as degree.
     * @return  int|float|null
     */
    public function track()
    {
        $key = 'GPSTrack';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $degrees = Exif::rational2Float($this->gpsData[$key]); // @phpstan-ignore-line
        return Exif::simplifyDegrees($degrees); // @phpstan-ignore-line
    }

    /**
     * returns track as strings.
     * @return  string|null
     */
    public function trackS()
    {
        $degrees = $this->track();
        if (is_null($degrees)) {
            return null;
        }
        $key = 'GPSTrackRef';
        return $this->formattedDirection($key, $degrees);
    }
}
