<?php

namespace Macocci7\PhpPhotoGps\Helpers;

use GuzzleHttp\Client;
use Macocci7\PhpPhotoGps\Helpers\Config;

/**
 * Class for Uri matter.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Uri
{
    /**
     * @var mixed[]|null   $config
     */
    private static array|null $config;

    private const URI_SCHEME_CHECK_PATTERN = '/^[A-Za-z0-9\+\-\.]+\:\/\//';
    private const URI_SCHEME_RETRIEVE_PATTERN = '/^([A-Za-z0-9\+\-\.]+)\:\/\//';

    /**
     * init
     * @return  void
     */
    public static function init()
    {
        if (!isset(self::$config)) {
            Config::load();
        }
        self::$config = Config::get();  // @phpstan-ignore-line
    }

    /**
     * returns config.
     * @param   string  $key = null
     * @return  mixed[]|null
     */
    public static function get(?string $key = null)
    {
        if (!isset(self::$config)) {
            self::init();
        }
        if (is_null($key)) {
            return self::$config;
        }
        if (!isset(self::$config[$key])) {
            return null;
        }
        return self::$config[$key]; // @phpstan-ignore-line
    }

    /**
     * judges if the path is available uri or not
     * @param   string  $uri
     * @return  bool
     */
    public static function isAvailable(string $uri)
    {
        $schemes = self::get('availableScheme');
        if (is_null($schemes)) {
            return false;
        }
        foreach ($schemes as $scheme) {
            if (str_starts_with($uri, $scheme)) {   // @phpstan-ignore-line
                return true;
            }
        }
        return false;
    }

    /**
     * judges if the uri is readable or not
     * @param   string  $uri
     * @return  bool
     */
    public static function isReadable(string $uri)
    {
        $client = new Client();
        try {
            $response = $client->request('HEAD', $uri);
            if ($response->getStatusCode() === 200) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * judges if the string is an uri or not
     * this method checkes if the string starts with uri scheme or not
     * @param   string  $string
     * @return  bool
     * @see https://developer.mozilla.org/ja/docs/Web/URI/Reference/Schemes
     */
    public static function isUri(string $string)
    {
        return preg_match(self::URI_SCHEME_CHECK_PATTERN, $string) === 1;
    }

    /**
     * returns shceme
     * @param   string  $uri
     * @return  string|null
     */
    public static function getScheme(string $uri)
    {
        if (!static::isUri($uri)) {
            return null;
        }
        preg_match(self::URI_SCHEME_RETRIEVE_PATTERN, $uri, $matches);
        return $matches[1] ?? null;
    }
}
