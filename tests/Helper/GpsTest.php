<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helper;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\Helper\Gps;
use Macocci7\PhpPhotoGps\Helper\Config;
use Nette\Neon\Neon;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class GpsTest extends TestCase
{
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public function test_init_can_load_config_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        Gps::init();
        $r = new \ReflectionClass(Gps::class);
        $p = $r->getProperty('def');
        $p->setAccessible(true);
        $this->assertSame(
            Neon::decodeFile($pathConf)['fields'],
            $p->getValue()
        );
    }

    public function test_def_can_return_value_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        // all
        $this->assertSame(
            $def,
            Gps::def()
        );
        // keys
        foreach ($def as $key => $value) {
            $this->assertSame(
                $value,
                Gps::def($key)
            );
        }
        // null
        $this->assertNull(Gps::def('hoge'));
    }

    public function test_type_can_return_value_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        // keys
        foreach ($def as $key => $tag) {
            $type = $tag['type'];
            $this->assertSame($type, Gps::type($key));
        }
        // null
        $this->assertNull(Gps::type('hoge'));
    }

    public function test_count_can_return_value_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        // keys
        foreach ($def as $key => $tag) {
            $count = $tag['count'];
            $this->assertSame($count, Gps::count($key));
        }
        // null
        $this->assertNull(Gps::count('hoge'));
    }

    public function test_values_can_return_value_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        // keys
        foreach ($def as $key => $tag) {
            if (isset($tag['values'])) {
                $values = $tag['values'];
                $this->assertSame($values, Gps::values($key));
            }
        }
        // null
        $this->assertNull(Gps::values('hoge'));
    }

    public static function provide_filter_can_filter_correctly(): array
    {
        return [
            "exif" => [
                'exif' => [
                    'gpshoge' => 'hoge',
                    'GPSHoge' => 'Hoge',
                    'GPsHogE' => 'HogE',
                    'GPSVersionID' => pack("C4", 2, 4, 0, 0),
                    'GPSAltitudeRef' => pack("C", 3),
                    'GPSProcessingMethod' => "\0\0\0\0GPS",
                ],
                'expect' => [
                    'GPSHoge' => 'Hoge',
                    'GPSVersionID' => "2.4.0.0",
                    'GPSAltitudeRef' => "3",
                    'GPSProcessingMethod' => "GPS",
                ],
            ],
        ];
    }

    /**
     * @dataProvider provide_filter_can_filter_correctly
     */
    public function test_filter_can_filter_correctly(array $exif, array $expect): void
    {
        $this->assertSame($expect, Gps::filter($exif));
    }

    public static function provide_convert_can_convert_correctly(): array
    {
        return [
            "gps" => [
                'exif' => [
                    'gpshoge' => 'hoge',
                    'GPSHoge' => 'Hoge',
                    'GPsHogE' => 'HogE',
                    'GPSVersionID' => pack("C4", 2, 4, 0, 0),
                    'GPSAltitudeRef' => pack("C", 3),
                    'GPSProcessingMethod' => "\0\0\0\0GPS",
                ],
                'expect' => [
                    'gpshoge' => 'hoge',
                    'GPSHoge' => 'Hoge',
                    'GPsHogE' => 'HogE',
                    'GPSVersionID' => "2.4.0.0",
                    'GPSAltitudeRef' => "3",
                    'GPSProcessingMethod' => "GPS",
                ],
            ],
        ];
    }

    /**
     * @dataProvider provide_convert_can_convert_correctly
     */
    public function test_convert_can_convert_correctly(array $gps, array $expect): void
    {
        $this->assertSame($expect, Gps::convert($gps));
    }

    public function test_isDefByte_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        foreach ($def as $key => $element) {
            $this->assertSame(
                0 === strcmp('BYTE', $element['type']),
                Gps::isDefByte($key)
            );
        }
    }

    public function test_isDefShort_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        foreach ($def as $key => $element) {
            $this->assertSame(
                0 === strcmp('SHORT', $element['type']),
                Gps::isDefShort($key)
            );
        }
    }

    public function test_isDefAscii_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        foreach ($def as $key => $element) {
            $this->assertSame(
                0 === strcmp('ASCII', $element['type']),
                Gps::isDefAscii($key)
            );
        }
    }

    public function test_isDefRational_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        foreach ($def as $key => $element) {
            $this->assertSame(
                0 === strcmp('RATIONAL', $element['type']),
                Gps::isDefRational($key)
            );
        }
    }

    public function test_isDefUndefined_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf)['fields'];
        foreach ($def as $key => $element) {
            $this->assertSame(
                0 === strcmp('UNDEFINED', $element['type']),
                Gps::isDefUndefined($key)
            );
        }
    }
}
