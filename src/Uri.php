<?php

namespace Macocci7\PhpPhotoGps;

/**
 * Class for Uri matter.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Uri
{
    /**
     * judges if the path is available uri or not
     * @param   string  $path
     * @return  bool
     */
    public static function isAvailable(string $uri)
    {
        return str_starts_with($uri, 'http://')
               || str_starts_with($uri, 'https://')
               || str_starts_with($uri, 'file://')
               || str_starts_with($uri, 'ftp://')
        ;
    }
}
