<?php

declare(strict_types=1);

namespace Dot\GeoIP;

use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function fclose;
use function fopen;

use const CURLOPT_FAILONERROR;
use const CURLOPT_FILE;

final readonly class Client
{
    public function get(string $url, string $target): void
    {
        $fp = fopen($target, 'wb');
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_FILE        => $fp,
            CURLOPT_FAILONERROR => true,
        ]);

        curl_exec($ch);
        fclose($fp);
    }
}
