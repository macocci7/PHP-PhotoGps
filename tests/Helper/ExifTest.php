<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helper;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\Helper\Exif;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class ExifTest extends TestCase
{
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public static function provide_version_can_set_version_correctly(): array
    {
        return [
            "exif0210" => [ 'ExifVersion' => '0210', ],
            "exif0220" => [ 'ExifVersion' => '0220', ],
            "exif0221" => [ 'ExifVersion' => '0221', ],
            "exif0230" => [ 'ExifVersion' => '0230', ],
            "exif0231" => [ 'ExifVersion' => '0231', ],
            "exif0232" => [ 'ExifVersion' => '0232', ],
            "exif0300" => [ 'ExifVersion' => '0300', ],
        ];
    }

    /**
     * @dataProvider provide_version_can_set_version_correctly
     */
    public function test_version_can_set_version_correctly(string $version): void
    {
        Exif::version($version);
        $r = new \ReflectionClass(Exif::class);
        $p = $r->getProperty('version');
        $p->setAccessible(true);
        $this->assertSame($version, $p->getValue());
        $this->assertSame($version, Exif::version());
    }

    public static function provide_get_can_return_exif_data_correctly(): array
    {
        return [
            "http" => [ 'path' => 'http://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => [ 'GPSDateStamp' => '2018:03:31', ], ],
            "https" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'expect' => [ 'GPSDateStamp' => '2015:06:07', ], ],
            "local" => [ 'path' => 'example/img/with_gps.jpg', 'expect' => [ 'GPSDateStamp' => '2023:09:18', ], ],
        ];
    }

    /**
     * @dataProvider provide_get_can_return_exif_data_correctly
     */
    public function test_get_can_return_exif_data_correctly(string $path, array $expect): void
    {
        $tag = array_keys($expect)[0];
        $value = $expect[$tag];
        $this->assertSame($value, Exif::get($path)[$tag]);
    }

    public function provide_byte2array_can_convert_value_correctly(): array
    {
        return [
            "byte:2, count:1" => [ 'byte' => [ 1 => 2],],
            "byte:3210, count:2" => [ 'byte' => [ 1 => 3, 2 => 2, 3 => 1, 4 => 0, ], ],
        ];
    }

    /**
     * @dataProvider provide_byte2array_can_convert_value_correctly
     */
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

    /**
     * @dataProvider provide_byte2ascii_can_convert_value_correctly
     */
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

    /**
     * @dataProvider provide_rational2Float_can_return_value_correctly
     */
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

    /**
     * @dataProvider provide_isRational_can_judge_correctly
     */
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

    /**
     * @dataProvider provide_simplifyDegrees_can_simplify_degrees_correctly
     */
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

    /**
     * @dataProvider provide_stripNullByte_can_strip_null_byte_correctly
     */
    public function test_stripNullByte_can_strip_null_byte_correctly(string $strings, string $expect): void
    {
        $this->assertSame($expect, Exif::stripNullByte($strings));
    }
}
