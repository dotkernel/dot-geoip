<?php

declare(strict_types=1);

namespace Dot\GeoIP;

use function file_exists;
use function is_dir;
use function is_file;
use function mkdir;
use function rmdir;
use function unlink;

final class FileSystem
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function mkdir(string $path, int $mode = 0o777): void
    {
        if ($this->exists($path)) {
            return;
        }

        mkdir($path, $mode, true);
    }

    public function remove(string $path): void
    {
        if (! $this->exists($path)) {
            return;
        }

        if (is_file($path)) {
            unlink($path);
        }

        if (is_dir($path)) {
            rmdir($path);
        }
    }
}
