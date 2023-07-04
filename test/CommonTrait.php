<?php

declare(strict_types=1);

namespace DotTest\GeoIP;

use Dot\GeoIP\Service\LocationService;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

use function file_get_contents;
use function sprintf;

trait CommonTrait
{
    protected vfsStreamDirectory $fileSystem;
    protected array $config   = [];
    protected array $defaults = [
        'continent'    => [
            'code' => 'code',
            'name' => 'name',
        ],
        'country'      => [
            'isEuMember' => false,
            'isoCode'    => 'isoCode',
            'name'       => 'name',
        ],
        'organization' => [
            'asn'  => 123456,
            'name' => 'name',
        ],
        'latitude'     => 12.34,
        'longitude'    => 12.34,
        'timeZone'     => 'timeZone',
    ];

    protected function setup(): void
    {
        $sampleData       = $this->getSampleData();
        $this->fileSystem = vfsStream::setup('root', 0644, [
            'data' => [
                'geoip' => [
                    sprintf('%s.mmdb', LocationService::DATABASE_ASN)     => $sampleData,
                    sprintf('%s.mmdb', LocationService::DATABASE_CITY)    => $sampleData,
                    sprintf('%s.mmdb', LocationService::DATABASE_COUNTRY) => $sampleData,
                ],
            ],
        ]);

        $this->config = $this->generateConfig(
            sprintf('%s/data/geoip', $this->fileSystem->url())
        );
    }

    protected function getConfig(): array
    {
        return $this->config;
    }

    protected function generateConfig(string $targetDir): array
    {
        return [
            'targetDir' => $targetDir,
            'databases' => [
                LocationService::DATABASE_ASN     => [
                    'source' => 'https://download.db-ip.com/free/dbip-asn-lite-{year}-{month}.mmdb.gz',
                ],
                LocationService::DATABASE_CITY    => [
                    'source' => 'https://download.db-ip.com/free/dbip-city-lite-{year}-{month}.mmdb.gz',
                ],
                LocationService::DATABASE_COUNTRY => [
                    'source' => 'https://download.db-ip.com/free/dbip-country-lite-{year}-{month}.mmdb.gz',
                ],
            ],
        ];
    }

    protected function getSampleData(): bool|string
    {
        return file_get_contents(
            sprintf('%s/sample.mmdb', __DIR__)
        );
    }
}
