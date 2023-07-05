<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Service;

use Dot\GeoIP\Data\ContinentData;
use Dot\GeoIP\Data\CountryData;
use Dot\GeoIP\Data\LocationData;
use Dot\GeoIP\Service\LocationService;
use DotTest\GeoIP\CommonTrait;
use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\Asn;
use GeoIp2\Model\City;
use GeoIp2\Model\Country;
use InvalidArgumentException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use MaxMind\Db\Reader\Metadata;
use PHPUnit\Framework\TestCase;

use function basename;
use function sprintf;
use function sys_get_temp_dir;

class LocationServiceTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws InvalidDatabaseException|\PHPUnit\Framework\MockObject\Exception
     */
    public function testAccessors(): void
    {
        $readerService = $this->createMock(Reader::class);

        $locationService = new LocationService($this->getConfig());
        $locationService->setCountryReader($readerService);
        $this->assertInstanceOf(LocationService::class, $locationService);
        $this->assertInstanceOf(Reader::class, $locationService->getCountryReader());
        $locationService->setCityReader($readerService);
        $this->assertInstanceOf(LocationService::class, $locationService);
        $this->assertInstanceOf(Reader::class, $locationService->getCityReader());
        $locationService->setAsnReader($readerService);
        $this->assertInstanceOf(LocationService::class, $locationService);
        $this->assertInstanceOf(Reader::class, $locationService->getAsnReader());
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetConfigsReturnsValidArray(): void
    {
        $locationService = new LocationService($this->getConfig());
        $configs         = $locationService->getConfigs();
        $this->assertIsArray($configs);
        $this->assertArrayHasKey('targetDir', $configs);
        $this->assertIsString($configs['targetDir']);
        $this->assertNotEmpty($configs['targetDir']);
        $this->assertArrayHasKey('databases', $configs);
        $this->assertIsArray($configs['databases']);
        $this->assertNotEmpty($configs['databases']);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetConfigReturnsCorrectValueOnValidKey(): void
    {
        $locationService = new LocationService($this->getConfig());
        $value           = $locationService->getConfig('targetDir');
        $this->assertIsString($value);
        $this->assertNotEmpty($value);
        $value = $locationService->getConfig('databases');
        $this->assertIsArray($value);
        $this->assertNotEmpty($value);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetConfigReturnsNullOnInvalidKey(): void
    {
        $locationService = new LocationService($this->getConfig());
        $value           = $locationService->getConfig('test');
        $this->assertNull($value);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testDatabaseExistsValidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());
        $result          = $locationService->databaseExists(LocationService::DATABASE_ASN);
        $this->assertTrue($result);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testDatabaseExistsInvalidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());
        $result          = $locationService->databaseExists('test');
        $this->assertFalse($result);
    }

    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException|\PHPUnit\Framework\MockObject\Exception
     */
    public function testGetContinentReturnsContinentData(): void
    {
        $countryModel  = new Country([
            'continent' => $this->defaults['continent'],
        ]);
        $countryReader = $this->createMock(Reader::class);
        $countryReader
            ->expects($this->once())
            ->method('country')
            ->willReturn($countryModel);

        $data = (new LocationService($this->getConfig()))
            ->setCountryReader($countryReader)
            ->getContinent('1.1.1.1');
        $this->assertInstanceOf(ContinentData::class, $data);
    }

    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException|\PHPUnit\Framework\MockObject\Exception
     */
    public function testGetCountryReturnsCountryData(): void
    {
        $countryModel  = new Country([
            'country' => $this->defaults['country'],
        ]);
        $countryReader = $this->createMock(Reader::class);
        $countryReader
            ->expects($this->once())
            ->method('country')
            ->willReturn($countryModel);

        $data = (new LocationService($this->getConfig()))
            ->setCountryReader($countryReader)
            ->getCountry('1.1.1.1');
        $this->assertInstanceOf(CountryData::class, $data);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetDatabaseMetadataForValidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());
        $result          = $locationService->getDatabaseMetadata(LocationService::DATABASE_ASN);
        $this->assertInstanceOf(Metadata::class, $result);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetDatabaseMetadataForInvalidDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());
        $result          = $locationService->getDatabaseMetadata('test');
        $this->assertNull($result);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetDatabasePath(): void
    {
        $locationService = new LocationService($this->getConfig());
        $path            = $locationService->getDatabasePath(LocationService::DATABASE_ASN);
        $this->assertSame(
            sprintf('%s/%s.mmdb', $this->getConfig()['targetDir'], LocationService::DATABASE_ASN),
            $path
        );
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetDatabaseSource(): void
    {
        $locationService = new LocationService($this->getConfig());
        $source          = $locationService->getDatabaseSource(LocationService::DATABASE_ASN);
        $this->assertSame(
            sprintf(
                '%s/%s',
                sys_get_temp_dir(),
                basename($this->getConfig()['databases'][LocationService::DATABASE_ASN]['source'])
            ),
            $source
        );
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetDatabaseReaderForValidaDatabase(): void
    {
        $locationService = new LocationService($this->getConfig());
        $reader          = $locationService->getDatabaseReader(LocationService::DATABASE_ASN);
        $this->assertInstanceOf(Reader::class, $reader);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function testGetDatabaseReaderThrowsExceptionOnInvalidDatabase(): void
    {
        $database        = 'test';
        $locationService = new LocationService($this->getConfig());
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The file "%s/%s.mmdb" does not exist or is not readable.',
                $this->getConfig()['targetDir'],
                $database
            )
        );
        $locationService->getDatabaseReader($database);
    }

    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException|\PHPUnit\Framework\MockObject\Exception
     */
    public function testGetLocation(): void
    {
        $ipAddress  = '1.1.1.1';
        $asnModel   = new Asn([
            'ip_address'                   => $ipAddress,
            'prefix_len'                   => 1,
            'autonomousSystemNumber'       => 'number',
            'autonomousSystemOrganization' => 'org',
        ]);
        $cityModel  = new City([
            'continent' => $this->defaults['continent'],
            'country'   => $this->defaults['country'],
        ]);
        $asnReader  = $this->createMock(Reader::class);
        $cityReader = $this->createMock(Reader::class);
        $asnReader
            ->expects($this->once())
            ->method('asn')
            ->willReturn($asnModel);
        $cityReader
            ->expects($this->once())
            ->method('city')
            ->willReturn($cityModel);

        $data = (new LocationService($this->getConfig()))
            ->setAsnReader($asnReader)
            ->setCityReader($cityReader)
            ->getLocation($ipAddress);
        $this->assertInstanceOf(LocationData::class, $data);
    }

    /**
     * @throws Exception
     */
    public function testObfuscateIpv4AddressReturnsLastBitAsZero(): void
    {
        $locationService = new LocationService($this->getConfig());
        $obfuscated      = $locationService->obfuscateIpAddress('1.1.1.1');
        $this->assertSame('1.1.1.0', $obfuscated);
    }

    /**
     * @throws Exception
     */
    public function testObfuscateIpv6AddressReturnsLastBitAsZero(): void
    {
        $locationService = new LocationService($this->getConfig());
        $obfuscated      = $locationService->obfuscateIpAddress('0000:0000:0000:0000:0000:0000:0000:0001');
        $this->assertSame('0000:0000:0000:0000:0000:0000:0000:0', $obfuscated);
    }

    /**
     * @throws Exception
     */
    public function testObfuscateIpAddressThrowsExceptionOnInvalidIpAddress(): void
    {
        $locationService = new LocationService($this->getConfig());
        $this->expectExceptionMessage('Invalid IP address: test');
        $locationService->obfuscateIpAddress('test');
    }
}
