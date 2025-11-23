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

    public static function provide_isReadable_can_judge_correctly(): array
    {
        return [
            'http, 200' => [
                'uri' => 'http://macocci7.net/',
                'expect' => true,
            ],
            'http, 404' => [
                'uri' => 'http://macocci7.net/no-such-path',
                'expect' => false,
            ],
            'https, 200' => [
                'uri' => 'https://macocci7.net/',
                'expect' => true,
            ],
            'https, 404' => [
                'uri' => 'https://macocci7.net/no-such-path',
                'expect' => false,
            ],
        ];
    }

    #[DataProvider('provide_isReadable_can_judge_correctly')]
    public function test_isReadable_can_judge_correctly(string $uri, bool $expect): void
    {
        $this->assertSame($expect, Uri::isReadable($uri));
    }

    public static function provide_isUrl_can_judge_correctly(): array
    {
        return [
            'empty string, false' => [
                'string' => '',
                'expect' => false,
            ],
            'only lowercase letters, false' => [
                'string' => 'hoge',
                'expect' => false,
            ],
            'only uppercase letters, false' => [
                'string' => 'HOGE',
                'expect' => false,
            ],
            'only uppercase and lowercase letters, false' => [
                'string' => 'Hoge',
                'expect' => false,
            ],
            'only alphabet and numbers, false' => [
                'string' => 'Hoge123',
                'expect' => false,
            ],
            'alphabet, numbers and colon, false' => [
                'string' => 'Hoge123:',
                'expect' => false,
            ],
            'alphabet, numbers, colon and slash, false' => [
                'string' => 'Hoge123:/',
                'expect' => false,
            ],
            'alphabet, numbers, colon and double slash, true' => [
                'string' => 'Hoge123://',
                'expect' => true,
            ],
            'without tdl, true' => [
                'string' => 'http://foo/',
                'expect' => true,
            ],
            'alphabet and +, true' => [
                'string' => 'svn+ssh://hoge',
                'expect' => true,
            ],
            'alphabet and dot, true' => [
                'string' => 'custom.scheme://hoge',
                'expect' => true,
            ],
            'alphabet and hyphen, true' => [
                'string' => 'custom-scheme://hoge',
                'expect' => true,
            ],
            'alphabet and underscore, false' => [
                'string' => 'custom_scheme://hoge',
                'expect' => false,
            ],
            'alphabet and slash, false' => [
                'string' => 'custom/scheme://hoge',
                'expect' => false,
            ],
            'colon-started, false' => [
                'string' => ':https://hoge.com/',
                'expect' => false,
            ],
            'slash-started, false' => [
                'string' => '/https://hoge.com/',
                'expect' => false,
            ],
            'absolute file path, false' => [
                'string' => '/var/www/html/example.com',
                'expect' => false,
            ],
            'relative file path, false' => [
                'string' => 'html/example.com',
                'expect' => false,
            ],
            'relative file paht with dot slash, false' => [
                'string' => './html/example.com',
                'expect' => false,
            ],
        ];
    }

    #[DataProvider('provide_isUrl_can_judge_correctly')]
    public function test_isUrl_can_judge_correctly(string $string, bool $expect): void
    {
        $this->assertSame($expect, Uri::isUri($string));
    }

    public static function provide_getScheme_can_return_scheme_correctly(): array
    {
        return [
            'http' => [
                'uri' => 'http://example.com/',
                'expect' => 'http',
            ],
            'https' => [
                'uri' => 'https://example.com/',
                'expect' => 'https',
            ],
            'svn+ssh' => [
                'uri' => 'svn+ssh://example.com/',
                'expect' => 'svn+ssh',
            ],
            'file' => [
                'uri' => 'file:///var/www/html',
                'expect' => 'file',
            ],
            'custom.scheme' => [
                'uri' => 'custom.scheme://example.com/',
                'expect' => 'custom.scheme',
            ],
            'custom-scheme' => [
                'uri' => 'custom-scheme://example.com/',
                'expect' => 'custom-scheme',
            ],
            'null (filepath)' => [
                'uri' => '/var/www/html',
                'expect' => null,
            ],
            'null (invalid shceme)' => [
                'uri' => ':https://example.com/',
                'expect' => null,
            ],
        ];
    }

    #[DataProvider('provide_getScheme_can_return_scheme_correctly')]
    public function test_getScheme_can_return_scheme_correctly(string $uri, string|null $expect): void
    {
        $this->assertSame($expect, Uri::getScheme($uri));
    }
}
