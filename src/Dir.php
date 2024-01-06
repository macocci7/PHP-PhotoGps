<?php

namespace Macocci7\PhpPhotoGps;

use Macocci7\PhpPhotoGps\File;

/**
 * class for dir operation
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Dir
{
    /**
     * returns dir entries.
     * @param   string  $dir
     * @return  string[];
     */
    public static function glob(string $dir)
    {
        return glob($dir . '/*');
    }

    /**
     * clears dir entries.
     * @param   string  $dir
     */
    public static function clear(string $dir)
    {
        foreach (self::glob($dir) as $file) {
            File::remove($file);
        }
    }
}
