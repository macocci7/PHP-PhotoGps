<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helper;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpPhotoGps\Helper\Uri;
use Nette\Neon\Neon;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class UriTest extends TestCase
{
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public function test_init_can_load_config_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Uri.neon';
        Uri::init();
        $r = new \ReflectionClass(Uri::class);
        $p = $r->getProperty('config');
        $p->setAccessible(true);
        $this->assertSame(
            Neon::decodeFile($pathConf),
            $p->getValue()
        );
    }

    public function test_get_can_return_value_correctly(): void
    {
        $pathConf = __DIR__ . '/../../conf/Uri.neon';
        foreach (Neon::decodeFile($pathConf) as $key => $value) {
            $this->assertSame(
                $value,
                Uri::get($key)
            );
        }
    }

    public function provide_isAvailable_can_judge_correctly(): array
    {
        return [
            "empty" => [ 'uri' => '', 'expect' => false, ],
            "odd" => [ 'uri' => 'http', 'expect' => false, ],
            "http:" => [ 'uri' => 'http:', 'expect' => true, ],
            "https:" => [ 'uri' => 'https:', 'expect' => true, ],
            "ftp:" => [ 'uri' => 'ftp:', 'expect' => false, ],
            "file:" => [ 'uri' => 'file:', 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isAvailable_can_judge_correctly
     */
    public function test_isAvailable_can_judge_correctly(string $uri, bool $expect): void
    {
        $this->assertSame($expect, Uri::isAvailable($uri));
    }
}
