<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helper;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\Helper\File;
use Macocci7\PhpPhotoGps\Helper\Dir;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class FileTest extends TestCase
{
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public function provide_download_can_download_correctly(): array
    {
        return [
            "image 1" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'savePath' => '', ],
            "image 2" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'savePath' => 'download/remote_fake_gps_002.jpg', ],
        ];
    }

    /**
     * @dataProvider provide_download_can_download_correctly
     */
    public function test_download_can_download_correctly(string $uri, string $savePath): void
    {
        $dir = './download/';
        Dir::clear($dir);
        $downloadPath = File::download($uri, $savePath);
        if (strlen($savePath) > 0) {
            $this->assertSame($savePath, $downloadPath);
        }
        $this->assertTrue(!is_null($downloadPath) && strlen($downloadPath) > 0);
        $this->assertTrue(file_exists($downloadPath));
        $this->assertTrue(filesize($downloadPath) > 0);
    }

    public function test_newPath_can_return_new_path_correctly(): void
    {
        $uri = 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg';
        $dir = './download/';
        $base = 'hoge';
        $savePath = $dir . $base . '.jpg';
        File::download($uri, $savePath);
        $expect = $dir . $base . '_1.jpg';
        $this->assertSame($expect, File::newPath($savePath));
        copy($savePath, $expect);
        $expect = $dir . $base . '_2.jpg';
        $this->assertSame($expect, File::newPath($savePath));
        copy($savePath, $expect);
        $expect = $dir . $base . '_3.jpg';
        $this->assertSame($expect, File::newPath($savePath));
    }

    public static function tearDownAfterClass(): void
    {
        $dir = './download/';
        Dir::clear($dir);
        Dir::remove($dir);
    }
}
