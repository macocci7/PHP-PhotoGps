<?php declare(strict_types=1);

require('vendor/autoload.php');
require('src/PhotoGps.php');

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

    public function test_coord_can_return_coord_data(): void
    {
        $filename = 'example/img/latov.jpg';
        $pg = new PhotoGps();
        $coord = $pg->coord($filename);
        foreach ($this->keys as $key) {
            $this->assertTrue(array_key_exists($key, $coord));
        }
    }

    public function test_coord_can_return_empty_when_gps_does_not_exist(): void
    {
        $filename = 'example/img/IMG_1119.jpg';
        $pg = new PhotoGps();
        $coord = $pg->coord($filename);
        $this->assertTrue(empty($coord));
    }

    public function test_coord_can_return_null_when_file_does_not_exist(): void
    {
        $filename = 'example/img/not_found.jpg';
        $pg = new PhotoGps();
        $coord = $pg->coord($filename);
        $this->assertNull($coord);
    }

    public function test_s2d_can_return_correct_value(): void
    {
        $pg = new PhotoGps();
        $cases = [
            [ "param" => null, "return" => null, ],
            [ "param" => 0, "return" => null, ],
            [ "param" => 0.1, "return" => null, ],
            [ "param" => true, "return" => null, ],
            [ "param" => false, "return" => null, ],
            [ "param" => "a", "return" => null, ],
            [ "param" => [], "return" => null, ],
            [ "param" => [ 0, ], "return" => null, ],
            [ "param" => [ 0, 1, ], "return" => null, ],
            [ "param" => [ 0, 1, 2, ], "return" => null, ],
            [ "param" => ["0/1", "0/1", "0/1000", ], "return" => 0.0, ],
            [ "param" => ["37/1", "30/1", "30000/1000", ], "return" => 37.50833333333333, ],
        ];
        foreach ($cases as $case) {
            $this->assertSame($pg->s2d($case["param"]), $case["return"]);
        }
    }
}
