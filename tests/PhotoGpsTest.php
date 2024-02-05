<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\PhotoGps;
use Macocci7\PhpPhotoGps\Helper\Dir;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class PhotoGpsTest extends TestCase
{
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong
    private $langs = [ 'eng', 'ja', ];
    private $defaultLang = 'eng';
    private $defaultFormat = [
        'eng' => '{degrees:v}{degrees:u}{minutes:v}{minutes:u}{seconds:v}{seconds:u}{ref:u}',
        'ja' => '{ref:u}{degrees:v}{degrees:u}{minutes:v}{minutes:u}{seconds:v}{seconds:u}',
    ];
    private $defaultDirectionFormat = [
        'eng' => '{ref} {degrees:v}{degrees:u}',
        'ja' => '{ref} {degrees:v}{degrees:u}',
    ];
    private $defaultSpeedFormat = [
        'eng' => '{speed:v}{speed:u}',
        'ja' => '{speed:v}{speed:u}',
    ];

    public static function provide_load_can_throw_exception_with_invalid_path(): array
    {
        return [
            "blank path" => ['path' => '', ],
            "not-existent file" => ['path' => 'notfound.jpg', ],
        ];
    }

    /**
     * @dataProvider provide_load_can_throw_exception_with_invalid_path
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function test_load_can_throw_exception_with_invalid_path(string $path): void
    {
        $this->expectException(\Exception::class);
        $message = "[" . $path . "] is not readable.";
        $this->expectExceptionMessage($message);
        $pg = new PhotoGps($path);
    }

    public static function provide_load_can_load_gps_data_correctly(): array
    {
        return [
            "GPS tags included" => [
                'path' => 'example/img/with_gps.jpg',
                'expect' => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ['37/1', '3/1', '26187/1000'],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ['140/1', '53/1', '32790/1000'],
                    'GPSAltitude' => '1300/100',
                ],
            ],
            "GPS tags not included" => [
                'path' => 'example/img/without_gps.jpg',
                'expect' => [],
            ],
        ];
    }

    /**
     * @dataProvider provide_load_can_load_gps_data_correctly
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function test_load_can_load_gps_data_correctly(string $path, array $expect): void
    {
        $pg = new PhotoGps($path);
        if (empty($expect)) {
            $this->assertTrue(empty($pg->gps()));
        } else {
            foreach ($expect as $key => $value) {
                $gps = $pg->gps();
                $this->assertSame($value, $gps[$key]);
            }
        }
    }

    public function test_lang_can_set_lang_correctly(): void
    {
        $pg = new PhotoGps();
        foreach ($this->langs as $lang) {
            $this->assertSame($lang, $pg->lang($lang)->lang());
        }
    }

    public function test_lang_cannot_set_unsupported_lang(): void
    {
        $pg = new PhotoGps();
        $lang = 'hoge';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("$lang is not available.");
        $this->assertNull($pg->lang($lang));
        $this->assertSame($pg->lang(), $this->defaultLang);
    }

    public function test_langs_can_return_langs_correctly(): void
    {
        $pg = new PhotoGps();
        $this->assertSame($this->langs, $pg->langs());
    }

    public static function provide_format_can_return_current_format_with_no_param(): array
    {
        return [
            "lang:eng" => [ 'lang' => 'eng', 'expect' => '{degrees:v}{degrees:u}{minutes:v}{minutes:u}{seconds:v}{seconds:u}{ref:u}', ],
            "lang:ja" => [ 'lang' => 'ja', 'expect' => '{ref:u}{degrees:v}{degrees:u}{minutes:v}{minutes:u}{seconds:v}{seconds:u}', ],
        ];
    }

    /**
     * @dataProvider provide_format_can_return_current_format_with_no_param
     */
    public function test_format_can_return_current_format_with_no_param(string $lang, string $expect): void
    {
        $pg = new PhotoGps();
        $pg->lang($lang);
        $this->assertSame($expect, $pg->format());
    }

    public static function provide_format_can_set_format_correctly(): array
    {
        return [
            "l1:eng, l2:eng" => [ 'lang1' => 'eng', 'format' => 'hoge', 'lang2' => 'eng', 'expect' => 'hoge', ],
            "l1:eng, l2:ja" => [ 'lang1' => 'eng', 'format' => 'hoge', 'lang2' => 'ja', 'expect' => '{ref:u}{degrees:v}{degrees:u}{minutes:v}{minutes:u}{seconds:v}{seconds:u}', ],
            "l1:ja, l2:eng" => [ 'lang1' => 'ja', 'format' => 'hoge', 'lang2' => 'eng', 'expect' => '{degrees:v}{degrees:u}{minutes:v}{minutes:u}{seconds:v}{seconds:u}{ref:u}', ],
            "l1:ja, l2:ja" => [ 'lang1' => 'ja', 'format' => 'hoge', 'lang2' => 'ja', 'expect' => 'hoge', ],
        ];
    }

    /**
     * @dataProvider provide_format_can_set_format_correctly
     */
    public function test_format_can_set_format_correctly(string $lang1, string $format, string $lang2, string $expect): void
    {
        $pg = new PhotoGps();
        $pg->lang($lang1);
        $pg->format($format);
        $pg->lang($lang2);
        $this->assertSame($expect, $pg->format());
    }

    public function test_resetFormat_can_reset_format_correctly(): void
    {
        $pg = new PhotoGps();
        $pg->lang('eng')->format('foo');
        $pg->lang('ja')->format('hoge');
        $pg->lang('eng')->resetFormat();
        $this->assertSame($this->defaultFormat['eng'], $pg->lang('eng')->format());
        $this->assertSame('hoge', $pg->lang('ja')->format());
        $pg->lang('eng')->format('foo');
        $pg->lang('ja')->format('hoge');
        $pg->lang('ja')->resetFormat();
        $this->assertSame('foo', $pg->lang('eng')->format());
        $this->assertSame($this->defaultFormat['ja'], $pg->lang('ja')->format());
    }

    public static function provide_gps_can_return_gps_data_correctly(): array
    {
        return [
            "http" => [ 'path' => 'http://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => [ 'GPSDateStamp' => '2018:03:31', ], ],
            "https" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'expect' => [ 'GPSDateStamp' => '2015:06:07', ], ],
            "local" => [ 'path' => 'example/img/with_gps.jpg', 'expect' => [ 'GPSDateStamp' => '2023:09:18', ], ],
        ];
    }

    /**
     * @dataProvider provide_gps_can_return_gps_data_correctly
     */
    public function test_gps_can_return_gps_data_correctly(string $path, array $expect): void
    {
        $pg = new PhotoGps($path);
        $tag = array_keys($expect)[0];
        $value = $expect[$tag];
        $this->assertSame($value, $pg->gps()[$tag]);
    }

    public static function provide_hasGeo_can_judge_correctly(): array
    {
        return [
            "without geo" => [ 'path' => 'example/img/without_gps.jpg', 'expect' => false, ],
            "with geo" => [ 'path' => 'example/img/with_gps.jpg', 'expect' => true, ],
        ];
    }

    /**
     * @dataProvider provide_hasGeo_can_judge_correctly
     */
    public function test_hasGeo_can_judge_correctly(string $path, bool $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->hasGeo());
    }

    public static function provide_hasGps_can_judge_correctly(): array
    {
        return [
            "gps: null" => ['gps' => null, 'expect' => false, ],
            "gps: empty" => ['gps' => [], 'expect' => false, ],
            "gps: non-coord data" => ['gps' => ['GPS' => 'gps', ], 'expect' => true, ],
            "gps: without Altitude" => [
                'gps' => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ['37/1', '3/1', '26072/1000'],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ['140/1', '53/1', '22398/1000'],
                ],
                'expect' => true,
            ],
            "gps: correct data" => [
                'gps' => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ['37/1', '3/1', '26072/1000'],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ['140/1', '53/1', '22398/1000'],
                    'GPSAltitude' => '1700/100',
                ],
                'expect' => true,
            ],
        ];
    }

    /**
     * @dataProvider provide_hasGps_can_judge_correctly
     */
    public function test_hasGps_can_judge_correctly(array|null $gps, bool $expect): void
    {
        $pg = new PhotoGps();
        $pg->gpsData = $gps;
        $this->assertSame($expect, $pg->hasGps());
    }

    public static function provide_hasAltitude_can_judge_correctly(): array
    {
        return [
            "without altitude" => [ 'path' => 'example/img/without_gps.jpg', 'expect' => false, ],
            "with altitude" => [ 'path' => 'example/img/with_gps.jpg', 'expect' => true, ],
        ];
    }

    /**
     * @dataProvider provide_hasAltitude_can_judge_correctly
     */
    public function test_hasAltitude_can_judge_correctly(string $path, bool $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->hasAltitude());
    }

    public static function provide_s2d_can_return_correct_value(): array
    {
        return [
            "param: empty" => [ "param" => [], "expect" => null, ],
            "param: 1 element" => [ "param" => [ 0, ], "expect" => null, ],
            "param: 2 elements" => [ "param" => [ 0, 1, ], "expect" => null, ],
            "param: 3 invalid elements" => [ "param" => [ 0, 1, 2, ], "expect" => null, ],
            "param: invalid latitude" => [ "param" => ["0/1/2", "0/1", "0/1000", ], "expect" => null, ],
            "param: all zero" => [ "param" => ["0/1", "0/1", "0/1000", ], "expect" => 0.0, ],
            "param: correct coord" => [ "param" => ["37/1", "30/1", "30000/1000", ], "expect" => 37.50833333333333, ],
        ];
    }

    /**
     * @dataProvider provide_s2d_can_return_correct_value
     */
    public function test_s2d_can_return_correct_value(array $param, float|null $expect): void
    {
        $pg = new PhotoGps();
        $this->assertSame($expect, $pg->s2d($param));
    }

    public static function provide_d2s_can_return_correct_values(): array
    {
        return [
            "param: negative value" => [ "param" => -1.2, "expect" => null, ],
            "param: positive value" => [ "param" => 136.27555555555557, "expect" => ["136/1", "16/1", "32000/1000", ], ],
        ];
    }

    /**
     * @dataProvider provide_d2s_can_return_correct_values
     */
    public function test_d2s_can_return_correct_values(float $param, array|null $expect): void
    {
        $pg = new PhotoGps();
        $this->assertSame($expect, $pg->d2s($param));
    }

    public static function provide_sexagesimal_can_return_value_correctly(): array
    {
        return [
            "eng, coord:empty, N" => [ "lang" => "eng", "coord" => [], "ref" => "N", "expect" => null, ],
            "eng, coord:invalid, N" => [ "lang" => "eng", "coord" => [37, 26, 12.3 ], "ref" => "N", "expect" => null, ],
            "eng, coord:1 elm, N" => [ "lang" => "eng", "coord" => ["37", ], "ref" => "N", "expect" => null, ],
            "eng, coord:2 elm, N" => [ "lang" => "eng", "coord" => ["37", "26/1", ], "ref" => "N", "expect" => null, ],
            "eng, coord:invalid latitude1, N" => [ "lang" => "eng", "coord" => ["37", "26/1", "12300/1000", ], "ref" => "N", "expect" => null, ],
            "eng, coord:invalid latitude2, N" => [ "lang" => "eng", "coord" => ["37/1/2", "26/1", "12300/1000", ], "ref" => "N", "expect" => null, ],
            "eng, coord:correct, n" => [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "n", "expect" => null, ],
            "eng, coord:correct, s" => [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "s", "expect" => null, ],
            "eng, corrd:correct, e" => [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "e", "expect" => null, ],
            "eng, coord:correct, w" => [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "w", "expect" => null, ],
            "eng, coord:correct, N" => [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "N", "expect" => "37°26'12.3\"N", ],
            "eng, coord:correct, S" => [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "S", "expect" => "37°26'12.3\"S", ],
            "eng, coord:correct, E" => [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "E", "expect" => "37°26'12.3\"E", ],
            "eng, coord:correct, W" => [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "W", "expect" => "37°26'12.3\"W", ],
            "ja, coord:correct, N" => [ "lang" => "ja", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "N", "expect" => "北緯37度26分12.3秒", ],
            "ja, coord:correct, S" => [ "lang" => "ja", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "S", "expect" => "南緯37度26分12.3秒", ],
            "ja, coord:correct, E" => [ "lang" => "ja", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "E", "expect" => "東経37度26分12.3秒", ],
            "ja, coord:correct, W" => [ "lang" => "ja", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "W", "expect" => "西経37度26分12.3秒", ],
        ];
    }

    /**
     * @dataProvider provide_sexagesimal_can_return_value_correctly
     */
    public function test_sexagesimal_can_return_value_correctly(string $lang, array $coord, string $ref, string|null $expect): void
    {
        $pg = new PhotoGps();
        $this->assertSame($expect, $pg->lang($lang)->sexagesimal($coord, $ref));
    }

    public static function provide_decimal_can_return_value_correctly(): array
    {
        return [
            "coord:empty, N" => [ "coord" => [], "ref" => "N", "expect" => null, ],
            "coord:1 elm, N" => [ "coord" => ["37/1", ], "ref" => "N", "expect" => null, ],
            "coord:2 elm, N" => [ "coord" => ["37/1", "30/1", ], "ref" => "N", "expect" => null, ],
            "coord:3 invalid elm, N" => [ "coord" => [37, 30, 30, ], "ref" => "N", "expect" => null, ],
            "coord:invalid latitude1, N" => [ "coord" => ["37", "30/1", "30000/1000", ], "ref" => "N", "expect" => null, ],
            "coord:invalid latitude2, N" => [ "coord" => ["37/1/2", "30/1", "30000/1000", ], "ref" => "N", "expect" => null, ],
            "coord:correct, n" => [ "coord" => ["37/1", "30/1", "30000/1000", ], "ref" => "n", "expect" => null, ],
            "coord:correct, s" => [ "coord" => ["37/1", "30/1", "30000/1000", ], "ref" => "s", "expect" => null, ],
            "coord:correct, e" => [ "coord" => ["136/1", "16/1", "32000/1000", ], "ref" => "e", "expect" => null, ],
            "coord:correct, w" => [ "coord" => ["136/1", "16/1", "32000/1000", ], "ref" => "w", "expect" => null, ],
            "coord:correct, N" => [ "coord" => ["37/1", "30/1", "30000/1000", ], "ref" => "N", "expect" => 37.50833333333333, ],
            "coord:correct, S" => [ "coord" => ["37/1", "30/1", "30000/1000", ], "ref" => "S", "expect" => -37.50833333333333, ],
            "coord:correct, E" => [ "coord" => ["136/1", "16/1", "32000/1000", ], "ref" => "E", "expect" => 136.27555555555557, ],
            "coord:correct, W" => [ "coord" => ["136/1", "16/1", "32000/1000", ], "ref" => "W", "expect" => -136.27555555555557, ],
        ];
    }

    /**
     * @dataProvider provide_decimal_can_return_value_correctly
     */
    public function test_decimal_can_return_value_correctly(array $coord, string $ref, float|null $expect): void
    {
        $pg = new PhotoGps();
        $this->assertSame($expect, $pg->decimal($coord, $ref));
    }

    public static function provide_latitudeS_can_return_value_correctly(): array
    {
        return [
            "gps:empty" => [ "gps" => [], "expect" => null, ],
            "gps:correct" => [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => "37°30'30.0\"N",
            ],
        ];
    }

    /**
     * @dataProvider provide_latitudeS_can_return_value_correctly
     */
    public function test_latitudeS_can_return_value_correctly(array $gps, string|null $expect): void
    {
        $pg = new PhotoGps();
        $pg->gpsData = $gps;
        $this->assertSame($expect, $pg->latitudeS());
    }

    public static function provide_latitudeD_can_return_value_correctly(): array
    {
        return [
            "gps:empty" => [ "gps" => [], "expect" => null, ],
            "gps:correct, N, E" => [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => 37.50833333333333,
            ],
            "gps:correct, S, E" => [
                "gps" => [
                    'GPSLatitudeRef' => 'S',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => -37.50833333333333,
            ],
        ];
    }

    /**
     * @dataProvider provide_latitudeD_can_return_value_correctly
     */
    public function test_latitudeD_can_return_value_correctly(array $gps, float|null $expect): void
    {
        $pg = new PhotoGps();
        $pg->gpsData = $gps;
        $this->assertSame($expect, $pg->latitudeD());
    }

    public static function provide_longitudeS_can_return_value_correctly(): array
    {
        return [
            "gps:empty" => [ "gps" => [], "expect" => null, ],
            "gps:correct" => [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => "136°16'32.0\"E",
            ],
        ];
    }

    /**
     * @dataProvider provide_longitudeS_can_return_value_correctly
     */
    public function test_longitudeS_can_return_value_correctly(array $gps, string|null $expect): void
    {
        $pg = new PhotoGps();
        $pg->gpsData = $gps;
        $this->assertSame($expect, $pg->longitudeS());
    }

    public static function provide_longitudeD_can_return_value_correctly(): array
    {
        return [
            "gps:empty" => [ "gps" => [], "expect" => null, ],
            "gps:correct, N, E" => [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => 136.27555555555557,
            ],
            "gps:correct, N, W" => [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'W',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => -136.27555555555557,
            ],
        ];
    }

    /**
     * @dataProvider provide_longitudeD_can_return_value_correctly
     */
    public function test_longitudeD_can_return_value_correctly(array $gps, float|null $expect): void
    {
        $pg = new PhotoGps();
        $pg->gpsData = $gps;
        $this->assertSame($expect, $pg->longitudeD());
    }

    public static function provide_altitude_can_return_value_correctly(): array
    {
        return [
            "gps:empty" => [ "gps" => [], "expect" => null, ],
            "gps:correct" => [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => 17.0,
            ],
        ];
    }

    /**
     * @dataProvider provide_altitude_can_return_value_correctly
     */
    public function test_altitude_can_return_value_correctly(array $gps, float|null $expect): void
    {
        $pg = new PhotoGps();
        $pg->gpsData = $gps;
        $this->assertSame($expect, $pg->altitude());
    }

    public static function provide_altitudeS_can_return_value_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'lang' => 'eng', 'expect' => null, ],
            "image 1, lang:eng" => [ 'path' => 'example/img/with_gps.jpg', 'lang' => 'eng', 'expect' => '(Above Ellipsoidal Surface) 13.00 m', ],
            "image 1, lang:ja" => [ 'path' => 'example/img/with_gps.jpg', 'lang' => 'ja', 'expect' => '（正値楕円体高） 13.00 メートル', ],
            "image 2, lang:eng" => [ 'path' => 'example/img/fake_gps_003.jpg', 'lang' => 'eng', 'expect' => '(Below Sea Level) 422.00 m', ],
            "image 2, lang:ja" => [ 'path' => 'example/img/fake_gps_003.jpg', 'lang' => 'ja', 'expect' => '（負値海抜） 422.00 メートル', ],
        ];
    }

    /**
     * @dataProvider provide_altitudeS_can_return_value_correctly
     */
    public function test_altitudeS_can_return_value_correctly(string $path, string $lang, string|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->lang($lang)->altitudeS());
    }

    public static function provide_direction_can_return_direction_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'expect' => null, ],
            "image 1" => [ 'path' => 'example/img/with_gps.jpg', 'expect' => 306.25440000000003, ],
            "image 2" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => 352.3981981981982, ],
        ];
    }

    /**
     * @dataProvider provide_direction_can_return_direction_correctly
     */
    public function test_direction_can_return_direction_correctly(string $path, float|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->direction());
    }

    public static function provide_directionS_can_return_direction_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'lang' => 'eng', 'expect' => null, ],
            "image 1, lang:eng" => [ 'path' => 'example/img/with_gps.jpg', 'lang' => 'eng', 'expect' => 'T 306.25°', ],
            "image 1, lang:ja" => [ 'path' => 'example/img/with_gps.jpg', 'lang' => 'ja', 'expect' => '真北 306.25度', ],
            "image 2, lang:eng" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'lang' => 'eng', 'expect' => 'M 352.40°', ],
            "image 2, lang:ja" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'lang' => 'ja', 'expect' => '磁北 352.40度', ],
        ];
    }

    /**
     * @dataProvider provide_directionS_can_return_direction_correctly
     */
    public function test_directionS_can_return_direction_correctly(string $path, string $lang, string|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->lang($lang)->directionS());
    }

    public static function provide_directionFormat_can_return_current_format_with_no_param(): array
    {
        return [
            "lang:eng" => [ 'lang' => 'eng', 'expect' => '{ref} {degrees:v}{degrees:u}', ],
            "lang:ja" => [ 'lang' => 'ja', 'expect' => '{ref} {degrees:v}{degrees:u}', ],
        ];
    }

    /**
     * @dataProvider provide_directionFormat_can_return_current_format_with_no_param
     */
    public function test_directionFormat_can_return_current_format_with_no_param(string $lang, string $expect): void
    {
        $pg = new PhotoGps();
        $pg->lang($lang);
        $this->assertSame($expect, $pg->directionFormat());
    }

    public static function provide_directionFormat_can_set_format_correctly(): array
    {
        return [
            "l1:eng, l2:eng" => [ 'lang1' => 'eng', 'format' => 'hoge', 'lang2' => 'eng', 'expect' => 'hoge', ],
            "l1:eng, l2:ja" => [ 'lang1' => 'eng', 'format' => 'hoge', 'lang2' => 'ja', 'expect' => '{ref} {degrees:v}{degrees:u}', ],
            "l1:ja, l2:eng" => [ 'lang1' => 'ja', 'format' => 'hoge', 'lang2' => 'eng', 'expect' => '{ref} {degrees:v}{degrees:u}', ],
            "l1:ja, l2:ja" => [ 'lang1' => 'ja', 'format' => 'hoge', 'lang2' => 'ja', 'expect' => 'hoge', ],
        ];
    }

    /**
     * @dataProvider provide_directionFormat_can_set_format_correctly
     */
    public function test_directionFormat_can_set_format_correctly(string $lang1, string $format, string $lang2, string $expect): void
    {
        $pg = new PhotoGps();
        $pg->lang($lang1);
        $pg->directionFormat($format);
        $pg->lang($lang2);
        $this->assertSame($expect, $pg->directionFormat());
    }

    public function test_resetDirectionFormat_can_reset_format_correctly(): void
    {
        $pg = new PhotoGps();
        $pg->lang('eng')->directionFormat('foo');
        $pg->lang('ja')->directionFormat('hoge');
        $pg->lang('eng')->resetDirectionFormat();
        $this->assertSame($this->defaultDirectionFormat['eng'], $pg->lang('eng')->directionFormat());
        $this->assertSame('hoge', $pg->lang('ja')->directionFormat());
        $pg->lang('eng')->directionFormat('foo');
        $pg->lang('ja')->directionFormat('hoge');
        $pg->lang('ja')->resetDirectionFormat();
        $this->assertSame('foo', $pg->lang('eng')->directionFormat());
        $this->assertSame($this->defaultDirectionFormat['ja'], $pg->lang('ja')->directionFormat());
    }

    public static function provide_speed_can_return_speed_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'expect' => null, ],
            "image 1" => [ 'path' => 'example/img/with_gps.jpg', 'expect' => 1.60, ],
            "image 2" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => 12.453799890650629, ],
        ];
    }

    /**
     * @dataProvider provide_speed_can_return_speed_correctly
     */
    public function test_spped_can_return_speed_correctly(string $path, float|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->speed());
    }

    public static function provide_speedS_can_return_speed_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'lang' => 'eng', 'expect' => null, ],
            "image 1, lang:eng" => [ 'path' => 'example/img/with_gps.jpg', 'lang' => 'eng', 'expect' => '1.60mph', ],
            "image 1, lang:ja" => [ 'path' => 'example/img/with_gps.jpg', 'lang' => 'ja', 'expect' => '1.60マイル／時', ],
            "image 2, lang:eng" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'lang' => 'eng', 'expect' => '12.45kt', ],
            "image 2, lang:ja" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'lang' => 'ja', 'expect' => '12.45ノット', ],
        ];
    }

    /**
     * @dataProvider provide_speedS_can_return_speed_correctly
     */
    public function test_speedS_can_return_speed_correctly(string $path, string $lang, string|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->lang($lang)->speedS());
    }

    public static function provide_speedFormat_can_return_current_format_with_no_param(): array
    {
        return [
            "lang:eng" => [ 'lang' => 'eng', 'expect' => '{speed:v}{speed:u}', ],
            "lang:ja" => [ 'lang' => 'ja', 'expect' => '{speed:v}{speed:u}', ],
        ];
    }

    /**
     * @dataProvider provide_speedFormat_can_return_current_format_with_no_param
     */
    public function test_speedFormat_can_return_current_format_with_no_param(string $lang, string $expect): void
    {
        $pg = new PhotoGps();
        $pg->lang($lang);
        $this->assertSame($expect, $pg->speedFormat());
    }

    public static function provide_speedFormat_can_set_format_correctly(): array
    {
        return [
            "l1:eng, l2:eng" => [ 'lang1' => 'eng', 'format' => 'hoge', 'lang2' => 'eng', 'expect' => 'hoge', ],
            "l1:eng, l2:ja" => [ 'lang1' => 'eng', 'format' => 'hoge', 'lang2' => 'ja', 'expect' => '{speed:v}{speed:u}', ],
            "l1:ja, l2:eng" => [ 'lang1' => 'ja', 'format' => 'hoge', 'lang2' => 'eng', 'expect' => '{speed:v}{speed:u}', ],
            "l1:ja, l2:ja" => [ 'lang1' => 'ja', 'format' => 'hoge', 'lang2' => 'ja', 'expect' => 'hoge', ],
        ];
    }

    /**
     * @dataProvider provide_speedFormat_can_set_format_correctly
     */
    public function test_speedFormat_can_set_format_correctly(string $lang1, string $format, string $lang2, string $expect): void
    {
        $pg = new PhotoGps();
        $pg->lang($lang1);
        $pg->speedFormat($format);
        $pg->lang($lang2);
        $this->assertSame($expect, $pg->speedFormat());
    }

    public function test_resetSpeedFormat_can_reset_format_correctly(): void
    {
        $pg = new PhotoGps();
        $pg->lang('eng')->speedFormat('foo');
        $pg->lang('ja')->speedFormat('hoge');
        $pg->lang('eng')->resetSpeedFormat();
        $this->assertSame($this->defaultSpeedFormat['eng'], $pg->lang('eng')->speedFormat());
        $this->assertSame('hoge', $pg->lang('ja')->speedFormat());
        $pg->lang('eng')->speedFormat('foo');
        $pg->lang('ja')->speedFormat('hoge');
        $pg->lang('ja')->resetSpeedFormat();
        $this->assertSame('foo', $pg->lang('eng')->speedFormat());
        $this->assertSame($this->defaultSpeedFormat['ja'], $pg->lang('ja')->speedFormat());
    }

    public static function provide_destBearing_can_return_dest_bearing_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'expect' => null, ],
            "image 1" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => 138.6932006633499, ],
            "image 2" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'expect' => 273.46589259796815, ],
        ];
    }

    /**
     * @dataProvider provide_destBearing_can_return_dest_bearing_correctly
     */
    public function test_destBearing_can_return_dest_bearing_correctly(string $path, float|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->destBearing());
    }

    public static function provide_destBearingS_can_return_dest_bearing_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'lang' => 'eng', 'expect' => null, ],
            "image 1, lang:eng" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'lang' => 'eng', 'expect' => 'M 138.69°', ],
            "image 1, lang:ja" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'lang' => 'ja', 'expect' => '磁北 138.69度', ],
            "image 2, lang:eng" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'lang' => 'eng', 'expect' => 'T 273.47°', ],
            "image 2, lang:ja" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'lang' => 'ja', 'expect' => '真北 273.47度', ],
        ];
    }

    /**
     * @dataProvider provide_destBearingS_can_return_dest_bearing_correctly
     */
    public function test_destBearingS_can_return_dest_bearing_correctly(string $path, string $lang, string|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->lang($lang)->destBearingS());
    }

    public static function provide_track_can_return_track_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'expect' => null, ],
            "image 1" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => 62.8972, ],
            "image 2" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'expect' => 268.44216417910445, ],
        ];
    }

    /**
     * @dataProvider provide_track_can_return_track_correctly
     */
    public function test_track_can_return_track_correctly(string $path, float|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->track());
    }

    public static function provide_trackS_can_return_track_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'lang' => 'eng', 'expect' => null, ],
            "image 1, lang:eng" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'lang' => 'eng', 'expect' => 'M 62.90°', ],
            "image 1, lang:ja" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'lang' => 'ja', 'expect' => '磁北 62.90度', ],
            "image 2, lang:eng" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'lang' => 'eng', 'expect' => 'T 268.44°', ],
            "image 2, lang:ja" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'lang' => 'ja', 'expect' => '真北 268.44度', ],
        ];
    }

    /**
     * @dataProvider provide_trackS_can_return_track_correctly
     */
    public function test_trackS_can_return_track_correctly(string $path, string $lang, string|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->lang($lang)->trackS());
    }

    public static function provide_datestamp_can_return_datestamp_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'expect' => null, ],
            "image 1" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => '2018/03/31', ],
            "image 2" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'expect' => '2015/06/07', ],
        ];
    }

    /**
     * @dataProvider provide_datestamp_can_return_datestamp_correctly
     */
    public function test_datestamp_can_return_datestamp_correctly(string $path, string|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->datestamp());
    }

    public static function provide_timestamp_can_return_timestamp_correctly(): array
    {
        return [
            "without gps" => [ 'path' => 'example/img/without_gps.jpg', 'expect' => null, ],
            "image 1" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_001.jpg', 'expect' => '02:22:14', ],
            "image 2" => [ 'path' => 'https://macocci7.net/photo/gps/remote_fake_gps_002.jpg', 'expect' => '01:31:46', ],
        ];
    }

    /**
     * @dataProvider provide_timestamp_can_return_timestamp_correctly
     */
    public function test_timestamp_can_return_timestamp_correctly(string $path, string|null $expect): void
    {
        $pg = new PhotoGps($path);
        $this->assertSame($expect, $pg->timestamp());
    }

    public static function tearDownAfterClass(): void
    {
        $dir = './download/';
        Dir::clear($dir);
        Dir::remove($dir);
    }
}
