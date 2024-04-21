<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helpers;

use Macocci7\PhpPhotoGps\Helpers\Dir;
use Macocci7\PhpPhotoGps\Helpers\File;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class FileTest extends TestCase
{
    public static function provide_download_can_download_correctly(): array
    {
        return [
            "image 1" => [ 'uri' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'savePath' => '', ],
            "image 2" => [ 'uri' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'savePath' => 'download/remote_fake_gps_002.jpg', ],
        ];
    }

    #[DataProvider('provide_download_can_download_correctly')]
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
