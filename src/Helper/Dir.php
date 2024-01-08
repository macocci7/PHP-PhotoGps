<?php

namespace Macocci7\PhpPhotoGps\Helper;

use Macocci7\PhpPhotoGps\Helper\File;

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
     * @return  array<int, string>|false
     */
    public static function glob(string $dir)
    {
        return glob($dir . '/*');
    }

    /**
     * clears dir entries.
     * @param   string  $dir
     * @return  void
     */
    public static function clear(string $dir)
    {
        $files = self::glob($dir);
        if (!$files) {
            return;
        }
        foreach ($files as $file) {
            File::remove($file);
        }
    }

    /**
     * remove directory
     * @param   string  $dir
     * @return  bool
     */
    public static function remove(string $dir)
    {
        return rmdir($dir);
    }
}
