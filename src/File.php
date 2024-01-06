<?php

namespace Macocci7\PhpPhotoGps;

use Macocci7\PhpPhotoGps\Uri;

/**
 * Class for File Handling.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class File
{
    /**
     * downloads uri into a local file.
     * @param   string  $uri
     * @param   string  $savePath = ''
     * @return  string  file path saved
     */
    public static function download(string $uri, ?string $savePath = '')
    {
        $dir = 'download';
        if (!file_exists($dir)) {
            if (!mkdir($dir)) {
                throw new \Exception("making dir $dir failed.");
            }
        }
        if (!is_dir($dir)) {
            throw new \Exception("$dir is not a directory.");
        }
        $date = new \DateTimeImmutable();
        $path = sprintf("%s/%s.jpg", $dir, $date->format("YmdHisu"));
        $path = self::newPath($path);
        if (!file_put_contents($path, file_get_contents($uri))) {
            return;
        }
        return $path;
    }

    /**
     * returns new unique file path for the param.
     * @param   string  $path
     * @return  string
     */
    public static function newPath(string $path)
    {
        $i = 0;
        $pathinfo = pathinfo($path);
        $dir = $pathinfo['dirname'];
        $base = $pathinfo['filename'];
        $ext = $pathinfo['extension'];
        while (file_exists($path)) {
            $i++;
            $path = sprintf("%s/%s_%d.%s", $dir, $base, $i, $ext);
        }
        return $path;
    }

    /**
     * remves the file or directory
     * @param   string  $path
     * @return  bool
     */
    public static function remove(string $path)
    {
        return unlink($path);
    }

    /**
     * saves the content into a file.
     * @param   string  $path
     * @param   string  $content
     * @return  int|bool
     */
    public static function save(string $path, string $content)
    {
        return file_put_contents($path, $content);
    }
}
