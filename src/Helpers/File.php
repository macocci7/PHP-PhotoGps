<?php

namespace Macocci7\PhpPhotoGps\Helpers;

use Macocci7\PhpPhotoGps\Helpers\Uri;

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
     * @return  string|null  file path saved
     * @thrown  \Exception
     */
    public static function download(string $uri, string $savePath = '')
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
        if (0 === strlen($savePath)) {
            $savePath = sprintf("%s/%s.jpg", $dir, $date->format("YmdHisu"));
            $savePath = self::newPath($savePath);
        }
        // @phpstan-ignore-next-line
        if (!self::save($savePath, file_get_contents($uri))) {
            return null;
        }
        return $savePath;
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
        $dir  = $pathinfo['dirname']; // @phpstan-ignore-line
        $base = $pathinfo['filename'];
        $ext  = $pathinfo['extension']; // @phpstan-ignore-line
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
