<?php

declare(strict_types=1);

namespace Macocci7\Sitemap;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\PhotoGps;

final class PhotoGpsTest extends TestCase
{
    /**
     * 取得対象のGPS関連EXIFタグ
     */
    private $keys = [
        'GPSLatitudeRef',   // 緯度基準（北緯 or 南緯）
        'GPSLatitude',  // 緯度数値（配列; 0:度/ 1:分/ 2:秒）
        'GPSLongitudeRef',  // 経度基準（東経 or 西経）
        'GPSLongitude', // 経度数値（配列; 0:度/ 1:分/ 2:秒）
        'GPSAltitude',  // 高度数値（cm）
    ];
    private $langs = [ 'eng', 'ja', ];

    public function test_load_can_throw_exception_with_invalid_path(): void
    {
        $cases = [
            ['path' => '', ],
            ['path' => 'notfound.jpg', ],
        ];
        foreach ($cases as $case) {
            $this->expectException(\Exception::class);
            $message = "[" . $case['path'] . "] is not readable.";
            $this->expectExceptionMessage($message);
            $pg = new PhotoGps($case['path']);
        }
    }

    public function test_load_can_load_gps_data_correctly(): void
    {
        $cases = [
            // GPS tags included
            [
                'path' => 'example/img/with_gps.jpg',
                'expect' => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ['37/1', '3/1', '26072/1000'],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ['140/1', '53/1', '22398/1000'],
                    'GPSAltitude' => '1700/100',
                ],
            ],
            // GPS tags not included
            [
                'path' => 'example/img/without_gps.jpg',
                'expect' => [],
            ],
        ];
        foreach ($cases as $case) {
            $pg = new PhotoGps($case['path']);
            if (empty($case['expect'])) {
                $this->assertTrue(empty($pg->gps()));
            } else {
                foreach ($case['expect'] as $key => $value) {
                    $gps = $pg->gps();
                    $this->assertSame($value, $gps[$key]);
                }
            }
        }
    }

    public function test_lang_can_set_lang_correctly(): void
    {
        $pg = new PhotoGps();
        foreach ($this->langs as $lang) {
            $this->assertSame($pg->lang($lang)->lang(), $lang);
        }
    }

    public function test_lang_cannot_set_unsupported_lang(): void
    {
        $pg = new PhotoGps();
        $this->assertNull($pg->lang('hoge'));
        $this->assertSame($pg->lang(), 'eng');
    }

    public function test_langs_can_return_langs_correctly(): void
    {
        $pg = new PhotoGps();
        $this->assertSame($pg->langs(), $this->langs);
    }

    public function test_hasGps_can_judge_correctly(): void
    {
        $cases = [
            ['gps' => null, 'expect' => false, ],
            ['gps' => [], 'expect' => false, ],
            ['gps' => ['GPS' => 'gps', ], 'expect' => false, ],
            [
                'gps' => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ['37/1', '3/1', '26072/1000'],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ['140/1', '53/1', '22398/1000'],
                ],
                'expect' => false,
            ],
            [
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
        $pg = new PhotoGps();
        foreach ($cases as $case) {
            $pg->gpsData = $case['gps'];
            $this->assertSame($case['expect'], $pg->hasGps());
        }
    }

    public function test_s2d_can_return_correct_value(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "param" => [], "return" => null, ],
            [ "param" => [ 0, ], "return" => null, ],
            [ "param" => [ 0, 1, ], "return" => null, ],
            [ "param" => [ 0, 1, 2, ], "return" => null, ],
            [ "param" => ["0/1/2", "0/1", "0/1000", ], "return" => null, ],
            [ "param" => ["0/1", "0/1", "0/1000", ], "return" => 0.0, ],
            [ "param" => ["37/1", "30/1", "30000/1000", ], "return" => 37.50833333333333, ],
        ];
        foreach ($cases as $case) {
            $this->assertSame($pg->s2d($case["param"]), $case["return"]);
        }
    }

    public function test_d2s_can_return_correct_values(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "param" => -1.2, "return" => null, ],
            [ "param" => 136.27555555555557, "return" => ["136/1", "16/1", "32000/1000", ], ],
        ];
        foreach ($cases as $case) {
            $this->assertSame($pg->d2s($case["param"]), $case["return"]);
        }
    }

    public function test_sexagesimal_can_return_value_correctly(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "lang" => "eng", "coord" => [], "ref" => "N", "expect" => null, ],
            [ "lang" => "eng", "coord" => [37, 26, 12.3 ], "ref" => "N", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37", ], "ref" => "N", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37", "26/1", ], "ref" => "N", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37", "26/1", "12300/1000", ], "ref" => "N", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37/1/2", "26/1", "12300/1000", ], "ref" => "N", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "n", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "s", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "e", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "w", "expect" => null, ],
            [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "N", "expect" => "37°26'12.3\"N", ],
            [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "S", "expect" => "37°26'12.3\"S", ],
            [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "E", "expect" => "37°26'12.3\"E", ],
            [ "lang" => "eng", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "W", "expect" => "37°26'12.3\"W", ],
            [ "lang" => "ja", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "N", "expect" => "37度26分12.3秒(北緯)", ],
            [ "lang" => "ja", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "S", "expect" => "37度26分12.3秒(南緯)", ],
            [ "lang" => "ja", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "E", "expect" => "37度26分12.3秒(東経)", ],
            [ "lang" => "ja", "coord" => ["37/1", "26/1", "12300/1000", ], "ref" => "W", "expect" => "37度26分12.3秒(西経)", ],
        ];
        foreach ($cases as $case) {
            $this->assertSame($pg->lang($case['lang'])->sexagesimal($case['coord'], $case['ref']), $case['expect']);
        }
    }

    public function test_decimal_can_return_value_correctly(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "coord" => [], "ref" => "N", "expect" => null, ],
            [ "coord" => ["37/1", ], "ref" => "N", "expect" => null, ],
            [ "coord" => ["37/1", "30/1", ], "ref" => "N", "expect" => null, ],
            [ "coord" => [37, 30, 30, ], "ref" => "N", "expect" => null, ],
            [ "coord" => ["37", "30/1", "30000/1000", ], "ref" => "N", "expect" => null, ],
            [ "coord" => ["37/1/2", "30/1", "30000/1000", ], "ref" => "N", "expect" => null, ],
            [ "coord" => ["37/1", "30/1", "30000/1000", ], "ref" => "n", "expect" => null, ],
            [ "coord" => ["37/1", "30/1", "30000/1000", ], "ref" => "s", "expect" => null, ],
            [ "coord" => ["136/1", "16/1", "32000/1000", ], "ref" => "e", "expect" => null, ],
            [ "coord" => ["136/1", "16/1", "32000/1000", ], "ref" => "w", "expect" => null, ],
            [ "coord" => ["37/1", "30/1", "30000/1000", ], "ref" => "N", "expect" => 37.50833333333333, ],
            [ "coord" => ["37/1", "30/1", "30000/1000", ], "ref" => "S", "expect" => -37.50833333333333, ],
            [ "coord" => ["136/1", "16/1", "32000/1000", ], "ref" => "E", "expect" => 136.27555555555557, ],
            [ "coord" => ["136/1", "16/1", "32000/1000", ], "ref" => "W", "expect" => -136.27555555555557, ],
        ];
        foreach ($cases as $case) {
            $this->assertSame($pg->decimal($case['coord'], $case['ref']), $case['expect']);
        }
    }

    public function test_latitudeS_can_return_value_correctly(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "gps" => [], "expect" => null, ],
            [
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
        foreach ($cases as $case) {
            $pg->gpsData = $case['gps'];
            $this->assertSame($pg->latitudeS(), $case['expect']);
        }
    }

    public function test_latitudeD_can_return_value_correctly(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "gps" => [], "expect" => null, ],
            [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => 37.50833333333333,
            ],
            [
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
        foreach ($cases as $case) {
            $pg->gpsData = $case['gps'];
            $this->assertSame($pg->latitudeD(), $case['expect']);
        }
    }

    public function test_longitudeS_can_return_value_correctly(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "gps" => [], "expect" => null, ],
            [
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
        foreach ($cases as $case) {
            $pg->gpsData = $case['gps'];
            $this->assertSame($pg->longitudeS(), $case['expect']);
        }
    }

    public function test_longitudeD_can_return_value_correctly(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "gps" => [], "expect" => null, ],
            [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => 136.27555555555557,
            ],
            [
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
        foreach ($cases as $case) {
            $pg->gpsData = $case['gps'];
            $this->assertSame($pg->longitudeD(), $case['expect']);
        }
    }

    public function test_altitude_can_return_value_correctly(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "gps" => [], "expect" => null, ],
            [
                "gps" => [
                    'GPSLatitudeRef' => 'N',
                    'GPSLatitude' => ["37/1", "30/1", "30000/1000", ],
                    'GPSLongitudeRef' => 'E',
                    'GPSLongitude' => ["136/1", "16/1", "32000/1000", ],
                    'GPSAltitude' => "1700/100",
                ],
                "expect" => 17,
            ],
        ];
        foreach ($cases as $case) {
            $pg->gpsData = $case['gps'];
            $this->assertSame($pg->altitude(), $case['expect']);
        }
    }
}
