<?php

use Dot\GeoIP\Handler\DatabaseHandler;

return [
    'dot_console' => [
        'name' => 'DotKernel API Console',
        'commands' => [
            [
                // php bin/console.php geoip:synchronize --help
                'name' => 'geoip:synchronize',
                'route' => 'geoip:synchronize --database=',
                'description' => 'Download latest version of GeoLite2-* database files.',
                'short_description' => 'Download latest version of GeoLite2-* database files.',
                'options_descriptions' => [
                    'database' => sprintf('Which database(s) to download (%s)',
                        implode(', ', DatabaseHandler::getValidIdentifiers())
                    )
                ],
                'handler' => DatabaseHandler::class
            ]
        ]
    ]
];
