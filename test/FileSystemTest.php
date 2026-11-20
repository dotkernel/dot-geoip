<?php

declare(strict_types=1);

namespace DotTest\GeoIP;

use Dot\GeoIP\Client;
use Dot\GeoIP\Extractor;
use Dot\GeoIP\FileSystem;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function file_get_contents;
use function file_put_contents;
use function gzencode;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

class FileSystemTest extends TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('root', null, [
            'file.txt' => 'content',
            'empty'    => [],
        ]);
    }

    public function testMkdirCreatesNestedDirectories(): void
    {
        $path = $this->root->url() . '/a/b';
        (new FileSystem())->mkdir($path);

        $this->assertDirectoryExists($path);
    }

    public function testRemoveDeletesFileAndEmptyDirectory(): void
    {
        $fileSystem = new FileSystem();
        $fileSystem->remove($this->root->url() . '/file.txt');
        $fileSystem->remove($this->root->url() . '/empty');

        $this->assertFileDoesNotExist($this->root->url() . '/file.txt');
        $this->assertDirectoryDoesNotExist($this->root->url() . '/empty');
    }

    public function testRemoveIgnoresMissingPath(): void
    {
        (new FileSystem())->remove($this->root->url() . '/missing');

        $this->assertFileDoesNotExist($this->root->url() . '/missing');
    }

    public function testExtractorWritesDecompressedContent(): void
    {
        $source = sprintf('%s/%s.gz', sys_get_temp_dir(), uniqid('dot-geoip-test-'));
        file_put_contents($source, gzencode('decompressed'));
        $target = $this->root->url() . '/target.txt';

        try {
            (new Extractor())->extract($source, $target);
        } finally {
            unlink($source);
        }

        $this->assertSame('decompressed', file_get_contents($target));
    }

    public function testExtractorThrowsWhenSourceIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot open "/nonexistent/dot-geoip.gz".');
        (new Extractor())->extract('/nonexistent/dot-geoip.gz', $this->root->url() . '/target.txt');
    }

    public function testClientThrowsWhenDownloadFails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The download from "file:///nonexistent/dot-geoip.gz" failed:');
        (new Client())->get('file:///nonexistent/dot-geoip.gz', $this->root->url() . '/target.gz');
    }
}
