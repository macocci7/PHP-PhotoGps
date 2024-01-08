<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helper;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\Helper\Config;
use Nette\Neon\Neon;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class ConfigTest extends TestCase
{
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public function test_load_can_load_config_file_correctly(): void
    {
        $from = __DIR__ . '/../../conf/PhotoGps.neon';
        $to = __DIR__ . '/../../conf/ConfigTest.neon';
        copy($from, $to);
        Config::load();
        $r = new \ReflectionClass(Config::class);
        $p = $r->getProperty('conf');
        $p->setAccessible(true);
        $this->assertSame(
            Neon::decodeFile($to),
            $p->getValue()[$this::class]
        );
        unlink($to);
    }

    public function return_class_name_from_config(): string|null
    {
        return Config::class();
    }

    public function test_class_can_return_class_name_correctly(): void
    {
        $this->assertSame($this::class, $this->return_class_name_from_config());
    }

    public static function provide_className_can_return_class_name_correctly(): array
    {
        return [
            "Fully Qualified" => [ 'class' => '\Macocci7\PhpPhotoGps\Helper\ConfigTest', 'expect' => 'ConfigTest', ],
            "Relative" => [ 'class' => 'Helper\ConfigTest', 'expect' => 'ConfigTest', ],
            "Only Class Name" => [ 'class' => 'ConfigTest', 'expect' => 'ConfigTest', ],
        ];
    }

    /**
     * @dataProvider provide_className_can_return_class_name_correctly
     */
    public function test_className_can_return_class_name_correctly(string $class, string $expect): void
    {
        $this->assertSame($expect, Config::className($class));
    }

    public function test_get_can_return_value_correctly(): void
    {
        $from = __DIR__ . '/../../conf/PhotoGps.neon';
        $to = __DIR__ . '/../../conf/ConfigTest.neon';
        copy($from, $to);
        Config::load();
        foreach (Neon::decodeFile($to) as $key => $value) {
            $this->assertSame(
                $value,
                Config::get($key)
            );
        }
        unlink($to);
    }
}
