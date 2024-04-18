<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\Helper\Arrow;
use Macocci7\PhpPhotoGps\Helper\File;
use Macocci7\PhpPhotoGps\Helper\Dir;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class ArrowTest extends TestCase
{
    public function test_make_can_return_instance_correctly(): void
    {
        $this->assertSame(
            \Intervention\Image\Image::class,
            Arrow::make(45.0)::class
        );
    }

    public static function provide_save_can_save_image_correctly(): array
    {
        return [
            "rotation: 0 degree" => [ 'degrees' => 0, 'expect' => '0.00', ],
            "rotation: 45.25 degrees" => [ 'degrees' => 45.25, 'expect' => '45.25', ],
            "rotation: 90.8263 degrees" => [ 'degrees' => 90.8263, 'expect' => '90.83', ],
            "rotation: 180.6425 degrees" => [ 'degrees' => 180.6425, 'expect' => '180.64', ],
            "rotation: 360.5 degrees" => [ 'degrees' => 360.5, 'expect' => '0.50', ],
            "rotation: 765.4682 degrees" => [ 'degrees' => 765.4682, 'expect' => '45.47', ],
        ];
    }

    #[DataProvider('provide_save_can_save_image_correctly')]
    public function test_save_can_save_image_correctly(int|float $degrees, string $expect): void
    {
        $dir = __DIR__ . '/img';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        Dir::clear($dir);
        $path = sprintf("%s/arrow%s.png", $dir, $expect);
        Arrow::make($degrees)->save($path);
        $this->assertTrue(file_exists($path));
    }

    public static function tearDownAfterClass(): void
    {
        $dir = __DIR__ . '/img/';
        Dir::clear($dir);
        Dir::remove($dir);
    }
}
