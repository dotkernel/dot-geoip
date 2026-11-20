<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Service;

use Dot\GeoIP\Service\LocationService;
use DotTest\GeoIP\CommonTrait;
use GeoIp2\Database\Reader;
use GeoIp2\Model\Asn;
use GeoIp2\Model\City;
use GeoIp2\Model\Country;
use InvalidArgumentException;
use MaxMind\Db\Reader\Metadata;
use PHPUnit\Framework\TestCase;

use function basename;
use function date;
use function sprintf;
use function sys_get_temp_dir;

class LocationServiceTest extends TestCase
{
    use CommonTrait;

    public function testAccessors(): void
    {
        $reader = $this->createStub(Reader::class);

        $locationService = new LocationService($this->getConfig());
        $this->assertSame($locationService, $locationService->setCountryReader($reader));
        $this->assertSame($reader, $locationService->getCountryReader());
        $this->assertSame($locationService, $locationService->setCityReader($reader));
        $this->assertSame($reader, $locationService->getCityReader());
        $this->assertSame($locationService, $locationService->setAsnReader($reader));
        $this->assertSame($reader, $locationService->getAsnReader());
    }

    public function testGetConfigsReturnsConfig(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertSame($this->getConfig(), $locationService->getConfigs());
    }

    public function testGetConfigReturnsCorrectValueOnValidKey(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertSame($this->getConfig()['targetDir'], $locationService->getConfig('targetDir'));
        $this->assertSame($this->getConfig()['databases'], $locationService->getConfig('databases'));
    }

    public function testGetConfigReturnsNullOnInvalidKey(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertNull($locationService->getConfig('test'));
    }

    public function testDatabaseExistsValidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertTrue($locationService->databaseExists(LocationService::DATABASE_ASN));
    }

    public function testDatabaseExistsInvalidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertFalse($locationService->databaseExists('test'));
    }

    public function testGetContinentReturnsContinentData(): void
    {
        $countryReader = $this->createMock(Reader::class);
        $countryReader
            ->expects($this->once())
            ->method('country')
            ->with('1.1.1.0')
            ->willReturn(new Country(['continent' => ['code' => 'EU', 'names' => ['en' => 'Europe']]]));

        $data = (new LocationService($this->getConfig()))
            ->setCountryReader($countryReader)
            ->getContinent('1.1.1.1');

        $this->assertSame('EU', $data->getCode());
        $this->assertSame('Europe', $data->getName());
        $this->assertNull($data->getError());
    }

    public function testGetContinentReturnsAnErrorWhenDatabaseIsMissing(): void
    {
        $data = (new LocationService($this->getConfig()))->getContinent('1.1.1.1');

        $this->assertSame($this->missingFileMessage(LocationService::DATABASE_COUNTRY), $data->getError());
    }

    public function testGetCountryReturnsCountryData(): void
    {
        $countryReader = $this->createMock(Reader::class);
        $countryReader
            ->expects($this->once())
            ->method('country')
            ->willReturn(new Country([
                'country' => ['iso_code' => 'RO', 'names' => ['en' => 'Romania'], 'is_in_european_union' => true],
            ]));

        $data = (new LocationService($this->getConfig()))
            ->setCountryReader($countryReader)
            ->getCountry('1.1.1.1');

        $this->assertSame('RO', $data->getIsoCode());
        $this->assertSame('Romania', $data->getName());
        $this->assertTrue($data->getIsEuMember());
        $this->assertNull($data->getError());
    }

    public function testGetCountryReturnsAnErrorWhenDatabaseIsMissing(): void
    {
        $data = (new LocationService($this->getConfig()))->getCountry('1.1.1.1');

        $this->assertFalse($data->getIsEuMember());
        $this->assertSame($this->missingFileMessage(LocationService::DATABASE_COUNTRY), $data->getError());
    }

    public function testGetCityReturnsCityData(): void
    {
        $cityReader = $this->createMock(Reader::class);
        $cityReader
            ->expects($this->once())
            ->method('city')
            ->willReturn(new City(['city' => ['names' => ['en' => 'Bucharest']]]));

        $data = (new LocationService($this->getConfig()))
            ->setCityReader($cityReader)
            ->getCity('1.1.1.1');

        $this->assertSame('Bucharest', $data->getName());
        $this->assertNull($data->getError());
    }

    public function testGetCityReturnsAnErrorWhenDatabaseIsMissing(): void
    {
        $data = (new LocationService($this->getConfig()))->getCity('1.1.1.1');

        $this->assertSame($this->missingFileMessage(LocationService::DATABASE_CITY), $data->getError());
    }

    public function testGetOrganizationReturnsDataFromRealDatabase(): void
    {
        $data = (new LocationService($this->getConfig()))->getOrganization('1.1.1.1');

        $this->assertSame(13335, $data->getAsn());
        $this->assertSame('Cloudflare, Inc.', $data->getName());
        $this->assertNull($data->getError());
    }

    public function testGetOrganizationReturnsAnErrorWhenAddressIsNotFound(): void
    {
        $data = (new LocationService($this->getConfig()))->getOrganization('10.0.0.1');

        $this->assertNull($data->getAsn());
        $this->assertSame('The address 10.0.0.0 is not in the database.', $data->getError());
    }

    public function testGetDatabaseMetadataForValidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertInstanceOf(Metadata::class, $locationService->getDatabaseMetadata(LocationService::DATABASE_ASN));
    }

    public function testGetDatabaseMetadataForInvalidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertNull($locationService->getDatabaseMetadata('test'));
    }

    public function testGetRealFilePath(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertSame(
            sprintf('%s/%s.mmdb', $this->getConfig()['targetDir'], LocationService::DATABASE_ASN),
            $locationService->getRealFilePath(LocationService::DATABASE_ASN)
        );
    }

    public function testGetTempFilePath(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertSame(
            sprintf(
                '%s/%s',
                sys_get_temp_dir(),
                basename($this->getConfig()['databases'][LocationService::DATABASE_ASN]['source'])
            ),
            $locationService->getTempFilePath(LocationService::DATABASE_ASN)
        );
    }

    public function testGetTempFilePathThrowsForDatabaseThatIsNotConfigured(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Database source URL not found');
        $locationService->getTempFilePath('test');
    }

    public function testGetDatabaseSourceUrlReplacesPlaceholders(): void
    {
        $config = $this->getConfig();
        $config['databases'][LocationService::DATABASE_ASN]['source'] = ' https://example.com/{year}-{month}.mmdb.gz ';

        $this->assertSame(
            sprintf('https://example.com/%s-%s.mmdb.gz', date('Y'), date('m')),
            (new LocationService($config))->getDatabaseSourceUrl(LocationService::DATABASE_ASN)
        );
    }

    public function testGetDatabaseSourceUrlThrowsForDatabaseThatIsNotConfigured(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Database source URL not found');
        $locationService->getDatabaseSourceUrl('test');
    }

    public function testGetDatabaseReaderForValidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertSame(
            'DBIP-ASN-Lite (compat=GeoLite2-ASN)',
            $locationService->getDatabaseReader(LocationService::DATABASE_ASN)->metadata()->databaseType
        );
    }

    public function testGetDatabaseReaderThrowsExceptionOnInvalidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->missingFileMessage('test'));
        $locationService->getDatabaseReader('test');
    }

    public function testGetLocation(): void
    {
        $asnReader = $this->createStub(Reader::class);
        $asnReader->method('asn')->willReturn(new Asn([
            'autonomous_system_number'       => $this->defaults['organization']['asn'],
            'autonomous_system_organization' => $this->defaults['organization']['name'],
            'ip_address'                     => '1.1.1.0',
            'prefix_len'                     => 24,
        ]));

        $cityReader = $this->createMock(Reader::class);
        $cityReader
            ->expects($this->once())
            ->method('city')
            ->with('1.1.1.0')
            ->willReturn(new City([
                'city'     => ['names' => ['en' => 'Bucharest']],
                'location' => [
                    'latitude'  => $this->defaults['latitude'],
                    'longitude' => $this->defaults['longitude'],
                    'time_zone' => $this->defaults['timeZone'],
                ],
            ]));

        $countryReader = $this->createStub(Reader::class);
        $countryReader->method('country')->willReturn(new Country([
            'continent' => ['code' => 'EU', 'names' => ['en' => 'Europe']],
            'country'   => ['iso_code' => 'RO', 'names' => ['en' => 'Romania'], 'is_in_european_union' => true],
        ]));

        $data = (new LocationService($this->getConfig()))
            ->setAsnReader($asnReader)
            ->setCityReader($cityReader)
            ->setCountryReader($countryReader)
            ->getLocation('1.1.1.1');

        $this->assertSame('EU', $data->getContinent()?->getCode());
        $this->assertSame('RO', $data->getCountry()?->getIsoCode());
        $this->assertSame('Bucharest', $data->getCity()?->getName());
        $this->assertSame($this->defaults['organization']['asn'], $data->getOrganization()?->getAsn());
        $this->assertSame($this->defaults['latitude'], $data->getLatitude());
        $this->assertSame($this->defaults['longitude'], $data->getLongitude());
        $this->assertSame($this->defaults['timeZone'], $data->getTimeZone());
    }

    public function testGetLocationThrowsWhenCityDatabaseIsMissing(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->missingFileMessage(LocationService::DATABASE_CITY));
        $locationService->getLocation('1.1.1.1');
    }

    public function testObfuscateIpv4AddressReturnsLastBitAsZero(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertSame('1.1.1.0', $locationService->obfuscateIpAddress('1.1.1.1'));
    }

    public function testObfuscateIpv6AddressReturnsLastBitAsZero(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertSame(
            '0000:0000:0000:0000:0000:0000:0000:0',
            $locationService->obfuscateIpAddress('0000:0000:0000:0000:0000:0000:0000:0001')
        );
    }

    public function testObfuscateIpv4MappedIpv6AddressKeepsIpv4Part(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->assertSame('::ffff:1.2.3.0', $locationService->obfuscateIpAddress('::ffff:1.2.3.4'));
    }

    public function testObfuscateIpAddressThrowsExceptionOnInvalidIpAddress(): void
    {
        $locationService = new LocationService($this->getConfig());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid IP address: test');
        $locationService->obfuscateIpAddress('test');
    }

    private function missingFileMessage(string $database): string
    {
        return sprintf(
            'The file "%s/%s.mmdb" does not exist or is not readable.',
            $this->getConfig()['targetDir'],
            $database
        );
    }
}
