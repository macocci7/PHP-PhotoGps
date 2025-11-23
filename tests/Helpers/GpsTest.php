<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helpers;

use Macocci7\PhpPhotoGps\Helpers\Config;
use Macocci7\PhpPhotoGps\Helpers\Exif;
use Macocci7\PhpPhotoGps\Helpers\Gps;
use Nette\Neon\Neon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class GpsTest extends TestCase
{
    public function test_init_can_load_config_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        Gps::init();
        $r = new \ReflectionClass(Gps::class);
        $p = $r->getProperty('configLoaded');
        $this->assertTrue($p->getValue());
        $r = new \ReflectionClass(Config::class);
        $p = $r->getProperty('conf');
        $this->assertSame(
            Neon::decodeFile($pathConf),
            $p->getValue()[Gps::class]
        );
    }

    public function test_def_can_return_value_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $def = Neon::decodeFile($pathConf);
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
        $defs = Neon::decodeFile($pathConf);
        // keys
        foreach ($defs as $exifVersion => $def) {
            $prefix = sprintf("%s.fields.", $exifVersion);
            foreach ($def['fields'] as $key => $tag) {
                $type = $tag['type'];
                $this->assertSame($type, Gps::type($prefix . $key));
            }
        }
        // null
        $this->assertNull(Gps::type('hoge'));
    }

    public function test_count_can_return_value_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $defs = Neon::decodeFile($pathConf);
        // keys
        foreach ($defs as $exifVersion => $def) {
            $prefix = sprintf("%s.fields.", $exifVersion);
            foreach ($def['fields'] as $key => $tag) {
                $count = $tag['count'];
                $this->assertSame($count, Gps::count($prefix . $key));
            }
        }
        // null
        $this->assertNull(Gps::count('hoge'));
    }

    public function test_values_can_return_value_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $defs = Neon::decodeFile($pathConf);
        // keys
        foreach ($defs as $exifVersion => $def) {
            $prefix = sprintf("%s.fields.", $exifVersion);
            foreach ($def['fields'] as $key => $tag) {
                if (isset($tag['values'])) {
                    $values = $tag['values'];
                    $this->assertSame($values, Gps::values($prefix . $key));
                }
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
                    'ExifVersion' => '0300',
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

    #[DataProvider('provide_filter_can_filter_correctly')]
    public function test_filter_can_filter_correctly(array $exif, array $expect): void
    {
        Exif::version($exif['ExifVersion']);
        $this->assertSame($expect, Gps::filter($exif));
    }

    public static function provide_convert_can_convert_correctly(): array
    {
        return [
            "gps" => [
                'gps' => [
                    'ExifVersion' => '0300',
                    'gpshoge' => 'hoge',
                    'GPSHoge' => 'Hoge',
                    'GPsHogE' => 'HogE',
                    'GPSVersionID' => pack("C4", 2, 4, 0, 0),
                    'GPSAltitudeRef' => pack("C", 3),
                    'GPSProcessingMethod' => "\0\0\0\0GPS",
                ],
                'expect' => [
                    'ExifVersion' => '0300',
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

    #[DataProvider('provide_convert_can_convert_correctly')]
    public function test_convert_can_convert_correctly(array $gps, array $expect): void
    {
        Exif::version($gps['ExifVersion']);
        $this->assertSame($expect, Gps::convert($gps));
    }

    public function test_isDefByte_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $defs = Neon::decodeFile($pathConf);
        foreach ($defs as $exifVersion => $def) {
            $prefix = sprintf("%s.fields.", $exifVersion);
            foreach ($def['fields'] as $key => $element) {
                $this->assertSame(
                    0 === strcmp('BYTE', $element['type']),
                    Gps::isDefByte($prefix . $key)
                );
            }
        }
    }

    public function test_isDefShort_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $defs = Neon::decodeFile($pathConf);
        foreach ($defs as $exifVersion => $def) {
            $prefix = sprintf("%s.fields.", $exifVersion);
            foreach ($def['fields'] as $key => $element) {
                $this->assertSame(
                    0 === strcmp('SHORT', $element['type']),
                    Gps::isDefShort($prefix . $key)
                );
            }
        }
    }

    public function test_isDefAscii_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $defs = Neon::decodeFile($pathConf);
        foreach ($defs as $exifVersion => $def) {
            $prefix = sprintf("%s.fields.", $exifVersion);
            foreach ($def['fields'] as $key => $element) {
                $this->assertSame(
                    0 === strcmp('ASCII', $element['type']),
                    Gps::isDefAscii($prefix . $key)
                );
            }
        }
    }

    public function test_isDefRational_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $defs = Neon::decodeFile($pathConf);
        foreach ($defs as $exifVersion => $def) {
            $prefix = sprintf("%s.fields.", $exifVersion);
            foreach ($def['fields'] as $key => $element) {
                $this->assertSame(
                    0 === strcmp('RATIONAL', $element['type']),
                    Gps::isDefRational($prefix . $key)
                );
            }
        }
    }

    public function test_isDefUndefined_can_judge_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Gps.neon';
        $defs = Neon::decodeFile($pathConf);
        foreach ($defs as $exifVersion => $def) {
            $prefix = sprintf("%s.fields.", $exifVersion);
            foreach ($def['fields'] as $key => $element) {
                $this->assertSame(
                    0 === strcmp('UNDEFINED', $element['type']),
                    Gps::isDefUndefined($prefix . $key)
                );
            }
        }
    }
}
