<?php

declare(strict_types=1);

namespace Dot\GeoIP;

use RuntimeException;

use function curl_error;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function fclose;
use function fopen;
use function sprintf;

use const CURLOPT_FAILONERROR;
use const CURLOPT_FILE;

final readonly class Client
{
    /**
     * @throws RuntimeException
     */
    public function get(string $url, string $target): void
    {
        $fp = fopen($target, 'wb');
        if ($fp === false) {
            throw new RuntimeException(sprintf('Cannot open "%s" for writing.', $target));
        }

        $ch = curl_init($url);
        if ($ch === false) {
            fclose($fp);
            throw new RuntimeException(sprintf('Cannot start the download from "%s".', $url));
        }

        curl_setopt_array($ch, [
            CURLOPT_FILE        => $fp,
            CURLOPT_FAILONERROR => true,
        ]);

        $result = curl_exec($ch);
        $error  = curl_error($ch);
        fclose($fp);

        if ($result === false) {
            throw new RuntimeException(sprintf('The download from "%s" failed: %s', $url, $error));
        }
    }
}
