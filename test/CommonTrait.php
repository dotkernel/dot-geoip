<?php

declare(strict_types=1);

namespace DotTest\GeoIP;

use Dot\GeoIP\Service\LocationService;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use RuntimeException;

use function date;
use function file_get_contents;
use function sprintf;

/**
 * @phpstan-import-type GeoIpConfig from LocationService
 */
trait CommonTrait
{
    use DefaultsTrait;

    protected vfsStreamDirectory $fileSystem;
    /** @var GeoIpConfig */
    protected array $config;

    protected function setUp(): void
    {
        $this->fileSystem = vfsStream::setup('root', 0644, [
            'data' => [
                'geoip' => [
                    sprintf('%s.mmdb', LocationService::DATABASE_ASN) => $this->getSampleData(),
                ],
            ],
        ]);

        $this->config = $this->generateConfig(
            sprintf('%s/data/geoip', $this->fileSystem->url())
        );
    }

    /**
     * @return GeoIpConfig
     */
    protected function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return GeoIpConfig
     */
    protected function generateConfig(string $targetDir): array
    {
        $year  = date('Y');
        $month = date('m');

        return [
            'targetDir' => $targetDir,
            'databases' => [
                LocationService::DATABASE_ASN     => [
                    'source' => sprintf('https://download.db-ip.com/free/dbip-asn-lite-%s-%s.mmdb.gz', $year, $month),
                ],
                LocationService::DATABASE_CITY    => [
                    'source' => sprintf('https://download.db-ip.com/free/dbip-city-lite-%s-%s.mmdb.gz', $year, $month),
                ],
                LocationService::DATABASE_COUNTRY => [
                    'source' => sprintf(
                        'https://download.db-ip.com/free/dbip-country-lite-%s-%s.mmdb.gz',
                        $year,
                        $month
                    ),
                ],
            ],
        ];
    }

    protected function getSampleData(): string
    {
        $data = file_get_contents(sprintf('%s/sample.mmdb', __DIR__));
        if ($data === false) {
            throw new RuntimeException('Cannot read the sample database.');
        }

        return $data;
    }
}
