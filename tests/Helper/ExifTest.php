<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\Helper\Exif;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class ExifTest extends TestCase
{
    public static function provide_version_can_set_version_correctly(): array
    {
        return [
            "exif0210" => [ 'exifVersion' => '0210', ],
            "exif0220" => [ 'exifVersion' => '0220', ],
            "exif0221" => [ 'exifVersion' => '0221', ],
            "exif0230" => [ 'exifVersion' => '0230', ],
            "exif0231" => [ 'exifVersion' => '0231', ],
            "exif0232" => [ 'exifVersion' => '0232', ],
            "exif0300" => [ 'exifVersion' => '0300', ],
        ];
    }

    #[DataProvider('provide_version_can_set_version_correctly')]
    public function test_version_can_set_version_correctly(string $exifVersion): void
    {
        Exif::version($exifVersion);
        $r = new \ReflectionClass(Exif::class);
        $p = $r->getProperty('version');
        $p->setAccessible(true);
        $this->assertSame($exifVersion, $p->getValue());
        $this->assertSame($exifVersion, Exif::version());
    }

    public static function provide_get_can_return_exif_data_correctly(): array
    {
        return [
            "http" => [ 'path' => 'http://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => [ 'GPSDateStamp' => '2018:03:31', ], ],
            "https" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'expect' => [ 'GPSDateStamp' => '2015:06:07', ], ],
            "local" => [ 'path' => 'example/img/with_gps.jpg', 'expect' => [ 'GPSDateStamp' => '2023:09:18', ], ],
        ];
    }

    #[DataProvider('provide_get_can_return_exif_data_correctly')]
    public function test_get_can_return_exif_data_correctly(string $path, array $expect): void
    {
        $tag = array_keys($expect)[0];
        $value = $expect[$tag];
        $this->assertSame($value, Exif::get($path)[$tag]);
    }

    public static function provide_byte2array_can_convert_value_correctly(): array
    {
        return [
            "byte:2, count:1" => [ 'byte' => [ 1 => 2],],
            "byte:3210, count:2" => [ 'byte' => [ 1 => 3, 2 => 2, 3 => 1, 4 => 0, ], ],
        ];
    }

    #[DataProvider('provide_byte2array_can_convert_value_correctly')]
    public function test_byte2array_can_convert_value_correctly(array $byte): void
    {
        $format = "C" . count($byte);
        $this->assertSame(
            $byte,
            Exif::byte2array(pack($format, ...$byte), count($byte))
        );
    }

    public static function provide_byte2ascii_can_convert_value_correctly(): array
    {
        return [
            "byte:2, count:1" => [ 'byte' => [2],],
            "byte:3210, count:2" => [ 'byte' => [3, 2, 1, 0, ], ],
        ];
    }

    #[DataProvider('provide_byte2ascii_can_convert_value_correctly')]
    public function test_byte2ascii_can_convert_value_correctly(array $byte): void
    {
        $format = "C" . count($byte);
        $this->assertSame(
            implode('', $byte),
            Exif::byte2ascii(pack($format, ...$byte), count($byte))
        );
    }

    public static function provide_rational2Float_can_return_value_correctly(): array
    {
        return [
            "not rational" => [ 'rational' => '1234', 'expect' => null, ],
            "divided by zero" => [ 'rational' => '1234/0', 'expect' => null, ],
            "rational 1" => [ 'rational' => '1234/1', 'expect' => 1234.0, ],
            "rational 2" => [ 'rational' => '1234/20', 'expect' => 61.7, ],
        ];
    }

    #[DataProvider('provide_rational2Float_can_return_value_correctly')]
    public function test_rational2Float_can_return_value_correctly(string $rational, float|null $expect): void
    {
        $this->assertSame($expect, Exif::rational2Float($rational));
    }

    public static function provide_isRational_can_judge_correctly(): array
    {
        return [
            "empty string" => [ 'rational' => '', 'expect' => false, ],
            "not rational 1" => [ 'rational' => '1234', 'expect' => false, ],
            "not rational 2" => [ 'rational' => '1234/a', 'expect' => false, ],
            "rational" => [ 'rational' => '1234/56', 'expect' => true, ],
        ];
    }

    #[DataProvider('provide_isRational_can_judge_correctly')]
    public function test_isRational_can_judge_correctly(string $rational, bool $expect): void
    {
        $this->assertSame($expect, Exif::isRational($rational));
    }

    public static function provide_simplifyDegrees_can_simplify_degrees_correctly(): array
    {
        return [
            "0" => [ 'degrees' => 0, 'expect' => 0, ],
            "1.5" => [ 'degrees' => 1.5, 'expect' => 1.5, ],
            "360" => [ 'degrees' => 360, 'expect' => 0, ],
            "360.5" => [ 'degrees' => 360.5, 'expect' => 0.5, ],
            "730" => [ 'degrees' => 730, 'expect' => 10, ],
            "732.5" => [ 'degrees' => 732.5, 'expect' => 12.5, ],
        ];
    }

    #[DataProvider('provide_simplifyDegrees_can_simplify_degrees_correctly')]
    public function test_simplifyDegrees_can_simplify_degrees_correctly(int|float $degrees, int|float $expect): void
    {
        $this->assertSame($expect, Exif::simplifyDegrees($degrees));
    }

    public static function provide_stripNullByte_can_strip_null_byte_correctly(): array
    {
        return [
            "empty strings" => [ 'strings' => '', 'expect' => '', ],
            "no null byte" => [ 'strings' => 'hoge', 'expect' => 'hoge', ],
            "1 null byte" => [ 'strings' => "\0hoge", 'expect' => 'hoge', ],
            "2 null bytes" => [ 'strings' => "\0hoge\0", 'expect' => 'hoge', ],
            "3 null bytes" => [ 'strings' => "\0\0\0hoge", 'expect' => 'hoge', ],
            "4 null bytes" => [ 'strings' => "\0\0\0\0hoge", 'expect' => 'hoge', ],
        ];
    }

    #[DataProvider('provide_stripNullByte_can_strip_null_byte_correctly')]
    public function test_stripNullByte_can_strip_null_byte_correctly(string $strings, string $expect): void
    {
        $this->assertSame($expect, Exif::stripNullByte($strings));
    }
}
