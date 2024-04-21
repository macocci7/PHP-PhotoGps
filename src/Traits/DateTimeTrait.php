<?php

namespace Macocci7\PhpPhotoGps\Traits;

use Macocci7\PhpPhotoGps\Helpers\Config;
use Macocci7\PhpPhotoGps\Helpers\Exif;

trait DateTimeTrait
{
    /**
     * returns converted date stamp
     * @return  string|null
     */
    public function datestamp()
    {
        $key = 'GPSDateStamp';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $items = explode(':', $this->gpsData[$key]); // @phpstan-ignore-line
        return date(
            $this->units[$this->lang()]['datestamp']['format'], // @phpstan-ignore-line
            strtotime(implode('/', $items)) // @phpstan-ignore-line
        );
    }

    /**
     * sets format of datestamp, or returns current format of datestamp without param
     * @param   string  $format = null
     * @return  self|string
     */
    public function datestampFormat(?string $format = null)
    {
        if (is_null($format)) {
            return $this->units[$this->lang()]['datestamp']['format']; // @phpstan-ignore-line
        }
        $this->units[$this->lang()]['datestamp']['format'] = $format; // @phpstan-ignore-line
        return $this;
    }

    /**
     * resets format of datestamp as default
     * @return  self
     */
    public function resetDatestampFormat()
    {
        $this->datestampFormat(
            // @phpstan-ignore-next-line
            Config::get('units')[$this->lang()]['datestamp']['format']
        );
        return $this;
    }

    /**
     * returns converted time stamp
     * @return  string|null
     */
    public function timestamp()
    {
        $key = 'GPSTimeStamp';
        if (!isset($this->gpsData[$key])) {
            return null;
        }
        $timestamp = $this->gpsData[$key];
        return date(
            $this->units[$this->lang()]['timestamp']['format'], // @phpstan-ignore-line
            strtotime( // @phpstan-ignore-line
                implode(
                    ':',
                    array_map(
                        fn ($v) => sprintf("%02d", Exif::rational2Float($v)), // @phpstan-ignore-line
                        $timestamp // @phpstan-ignore-line
                    )
                )
            )
        );
    }

    /**
     * sets format of timestamp, or returns current format of timestamp without param
     * @param   string  $format = null
     * @return  self|string
     */
    public function timestampFormat(?string $format = null)
    {
        if (is_null($format)) {
            return $this->units[$this->lang()]['timestamp']['format']; // @phpstan-ignore-line
        }
        $this->units[$this->lang()]['timestamp']['format'] = $format; // @phpstan-ignore-line
        return $this;
    }

    /**
     * resets format of timestamp as default
     * @return  self
     */
    public function resetTimestampFormat()
    {
        $this->timestampFormat(
            // @phpstan-ignore-next-line
            Config::get('units')[$this->lang()]['timestamp']['format']
        );
        return $this;
    }
}
