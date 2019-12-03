<?php

declare(strict_types=1);

namespace Dot\GeoIP\Service;

use Dot\GeoIP\Data\ContinentData;
use Dot\GeoIP\Data\CountryData;
use Dot\GeoIP\Data\LocationData;
use Dot\GeoIP\Data\OrganizationData;
use Exception;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\Metadata;
use MaxMind\Db\Reader\InvalidDatabaseException;

use function explode;
use function file_exists;
use function filter_var;
use function implode;

/**
 * Class LocationService
 * @package Dot\GeoIP\Service
 */
class LocationService implements LocationServiceInterface
{
    const DATABASE_ALL = 'all';
    const DATABASE_ASN = 'asn';
    const DATABASE_CITY = 'city';
    const DATABASE_COUNTRY = 'country';
    const DATABASES = [
        self::DATABASE_ASN => 'GeoLite2-ASN.mmdb',
        self::DATABASE_CITY => 'GeoLite2-City.mmdb',
        self::DATABASE_COUNTRY => 'GeoLite2-Country.mmdb'
    ];

    /** @var array $config */
    protected $config;

    /**
     * LocationService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $database
     * @return bool
     */
    public function databaseExists(string $database): bool
    {
        $path = $this->getDatabasePath($database);

        return file_exists($path);
    }

    /**
     * @param string $ipAddress
     * @return ContinentData
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getContinent(string $ipAddress): ContinentData
    {
        $reader = $this->getDatabaseReader(self::DATABASE_COUNTRY);
        $data = $reader->country($this->obfuscateIpAddress($ipAddress));

        $continent = new ContinentData();
        $continent->setCode($data->continent->code)->setName($data->continent->name);

        return $continent;
    }

    /**
     * @param string $ipAddress
     * @return CountryData
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getCountry(string $ipAddress): CountryData
    {
        $reader = $this->getDatabaseReader(self::DATABASE_COUNTRY);
        $data = $reader->country($this->obfuscateIpAddress($ipAddress));

        $country = new CountryData();
        $country
            ->setIsEuMember($data->country->isInEuropeanUnion)
            ->setIsoCode($data->country->isoCode)
            ->setName($data->country->name);

        return $country;
    }

    /**
     * @param string $database
     * @return Metadata|null
     */
    public function getDatabaseMetadata(string $database): ?Metadata
    {
        try {
            $reader = $this->getDatabaseReader($database);

            return $reader->metadata();
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param string $database
     * @return string
     */
    public function getDatabasePath(string $database): string
    {
        return $this->config['databases'][$database]['target'] . '/' . self::DATABASES[$database];
    }

    /**
     * @param string $database
     * @return Reader
     * @throws InvalidDatabaseException
     */
    public function getDatabaseReader(string $database): Reader
    {
        $path = $this->getDatabasePath($database);

        return new Reader($path);
    }

    /**
     * @param string $ipAddress
     * @return LocationData
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getLocation(string $ipAddress): LocationData
    {
        $ipAddress = $this->obfuscateIpAddress($ipAddress);

        $reader = $this->getDatabaseReader(self::DATABASE_ASN);
        $asnData = $reader->asn($ipAddress);

        $reader = $this->getDatabaseReader(self::DATABASE_CITY);
        $cityData = $reader->city($ipAddress);

        $continent = new ContinentData();
        $continent->setCode($cityData->continent->code)->setName($cityData->continent->name);

        $country = new CountryData();
        $country
            ->setIsEuMember($cityData->country->isInEuropeanUnion)
            ->setIsoCode($cityData->country->isoCode)
            ->setName($cityData->country->name);

        $organization = new OrganizationData();
        $organization->setAsn($asnData->autonomousSystemNumber)->setName($asnData->autonomousSystemOrganization);

        $location = new LocationData();
        $location
            ->setContinent($continent)
            ->setCountry($country)
            ->setLatitude($cityData->location->latitude)
            ->setLongitude($cityData->location->longitude)
            ->setOrganization($organization)
            ->setTimeZone($cityData->location->timeZone);

        return $location;
    }

    /**
     * @param string $ipAddress
     * @return OrganizationData
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getOrganization(string $ipAddress): OrganizationData
    {
        $reader = $this->getDatabaseReader(self::DATABASE_ASN);
        $data = $reader->asn($this->obfuscateIpAddress($ipAddress));

        $organization = new OrganizationData();
        $organization->setAsn($data->autonomousSystemNumber)->setName($data->autonomousSystemOrganization);

        return $organization;
    }

    /**
     * @param string $ipAddress
     * @return string
     * @throws Exception
     */
    public function obfuscateIpAddress(string $ipAddress): string
    {
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $bytes = explode('.', $ipAddress);
            $bytes[3] = '0';
            return implode('.', $bytes);
        }

//        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
//            $bytes = explode(':', $ipAddress);
//            $bytes[count($bytes) - 1] = '0000';
//            return implode(':', $bytes);
//        }

        throw new Exception('Invalid IP address: ' . $ipAddress);
    }
}
