<?php

declare(strict_types=1);

use Dot\GeoIP\Service\LocationService;

return [
    'dot-geoip' => [
        'targetDir' => getcwd() . '/data/geoip',
        'databases' => [
            LocationService::DATABASE_ASN => [
                'source' => 'https://download.db-ip.com/free/dbip-asn-lite-{year}-{month}.mmdb.gz',
            ],
            LocationService::DATABASE_CITY => [
                'source' => 'https://download.db-ip.com/free/dbip-city-lite-{year}-{month}.mmdb.gz',
            ],
            LocationService::DATABASE_COUNTRY => [
                'source' => 'https://download.db-ip.com/free/dbip-country-lite-{year}-{month}.mmdb.gz',
            ],
        ],
    ]
];
