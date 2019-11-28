<?php

declare(strict_types=1);

return [
    'dot-geoip' => [
        'databases' => [
            'city' => [
                'source' => 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz',
                'target' => getcwd() . '/data/geoip/city',
            ],
            'country' => [
                'source' => 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz',
                'target' => getcwd() . '/data/geoip/country',
            ],
            'asn' => [
                'source' => 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-ASN.tar.gz',
                'target' => getcwd() . '/data/geoip/asn',
            ],
        ],
        'tempDir' => sys_get_temp_dir()
    ]
];
