<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpPhotoGps\Helpers;

use Macocci7\PhpPhotoGps\Helpers\Uri;
use Nette\Neon\Neon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UriTest extends TestCase
{
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

    public static function provide_isAvailable_can_judge_correctly(): array
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

    #[DataProvider('provide_isAvailable_can_judge_correctly')]
    public function test_isAvailable_can_judge_correctly(string $uri, bool $expect): void
    {
        $this->assertSame($expect, Uri::isAvailable($uri));
    }
}
