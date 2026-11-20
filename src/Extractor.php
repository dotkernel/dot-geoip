<?php

declare(strict_types=1);

namespace Dot\GeoIP;

use RuntimeException;

use function fclose;
use function fopen;
use function fwrite;
use function gzclose;
use function gzeof;
use function gzopen;
use function gzread;
use function is_file;
use function is_readable;
use function sprintf;

final readonly class Extractor
{
    /**
     * @throws RuntimeException
     */
    public function extract(string $source, string $target): void
    {
        $in = is_file($source) && is_readable($source) ? gzopen($source, 'rb') : false;
        if ($in === false) {
            throw new RuntimeException(sprintf('Cannot open "%s".', $source));
        }

        $out = fopen($target, 'wb');
        if ($out === false) {
            gzclose($in);
            throw new RuntimeException(sprintf('Cannot open "%s" for writing.', $target));
        }

        try {
            while (! gzeof($in)) {
                $chunk = gzread($in, 8192);
                if ($chunk === false || fwrite($out, $chunk) === false) {
                    throw new RuntimeException(sprintf('Cannot extract "%s".', $source));
                }
            }
        } finally {
            gzclose($in);
            fclose($out);
        }
    }
}
