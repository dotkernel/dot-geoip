<?php

declare(strict_types=1);

namespace Dot\GeoIP;

use function fclose;
use function fopen;
use function fwrite;
use function gzclose;
use function gzeof;
use function gzopen;
use function gzread;

final readonly class Extractor
{
    public function extract(string $source, string $target): void
    {
        $in  = gzopen($source, 'rb');
        $out = fopen($target, 'wb');

        while (! gzeof($in)) {
            fwrite($out, gzread($in, 8192));
        }

        gzclose($in);
        fclose($out);
    }
}
