<?php

use Dot\GeoIP\Handler\DatabaseHandler;
use Dot\GeoIP\Service\LocationService;

return [
    'dot_console' => [
        'name' => 'DotKernel API Console',
        'commands' => [
            [
                // php bin/console.php geoip:synchronize
                'name' => 'geoip:synchronize',
                'route' => 'geoip:synchronize [--database=] [--quiet|-q]',
                'description' => 'Download latest version of GeoLite2-* database files.',
                'short_description' => 'Download latest version of GeoLite2-* database files.',
                'options_descriptions' => [
                    'database' => sprintf(
                        'Which database(s) to download (%s)',
                        implode(', ', LocationService::DATABASES)
                    ),
                    'quiet' => 'Do not output information.'
                ],
                'handler' => DatabaseHandler::class
            ]
        ]
    ]
];
