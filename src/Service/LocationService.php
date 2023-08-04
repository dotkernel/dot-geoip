<?php

declare(strict_types=1);

namespace Dot\GeoIP\Service;

use Dot\GeoIP\Data\ContinentData;
use Dot\GeoIP\Data\CountryData;
use Dot\GeoIP\Data\LocationData;
use Dot\GeoIP\Data\OrganizationData;
use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use MaxMind\Db\Reader\Metadata;
use Throwable;

use function array_pop;
use function basename;
use function explode;
use function file_exists;
use function filter_var;
use function implode;
use function rtrim;
use function sprintf;
use function sys_get_temp_dir;

use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_IP;

class LocationService implements LocationServiceInterface
{
    public const DATABASE_ALL     = 'all';
    public const DATABASE_ASN     = 'asn';
    public const DATABASE_CITY    = 'city';
    public const DATABASE_COUNTRY = 'country';
    public const DATABASES        = [
        self::DATABASE_ASN     => 'GeoLite2-ASN.mmdb',
        self::DATABASE_CITY    => 'GeoLite2-City.mmdb',
        self::DATABASE_COUNTRY => 'GeoLite2-Country.mmdb',
    ];

    protected ?Reader $countryReader = null;
    protected ?Reader $cityReader    = null;
    protected ?Reader $asnReader     = null;
    protected array $config          = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConfigs(): array
    {
        return $this->config;
    }

    public function getConfig(string $name): mixed
    {
        return $this->config[$name] ?? null;
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function getCountryReader(): Reader
    {
        if (! $this->countryReader instanceof Reader) {
            $this->countryReader = $this->getDatabaseReader(self::DATABASE_COUNTRY);
        }

        return $this->countryReader;
    }

    public function setCountryReader(Reader $countryReader): self
    {
        $this->countryReader = $countryReader;

        return $this;
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function getCityReader(): Reader
    {
        if (! $this->cityReader instanceof Reader) {
            $this->cityReader = $this->getDatabaseReader(self::DATABASE_CITY);
        }

        return $this->cityReader;
    }

    public function setCityReader(Reader $cityReader): self
    {
        $this->cityReader = $cityReader;

        return $this;
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function getAsnReader(): Reader
    {
        if (! $this->asnReader instanceof Reader) {
            $this->asnReader = $this->getDatabaseReader(self::DATABASE_ASN);
        }

        return $this->asnReader;
    }

    public function setAsnReader(Reader $asnReader): self
    {
        $this->asnReader = $asnReader;

        return $this;
    }

    public function databaseExists(string $database): bool
    {
        $path = $this->getDatabasePath($database);

        return file_exists($path);
    }

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getContinent(string $ipAddress): ContinentData
    {
        $data = $this->getCountryReader()->country($this->obfuscateIpAddress($ipAddress));

        return (new ContinentData())
            ->setCode($data->continent->code ?? null)
            ->setName($data->continent->name ?? null);
    }

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getCountry(string $ipAddress): CountryData
    {
        $data = $this->getCountryReader()->country($this->obfuscateIpAddress($ipAddress));

        return (new CountryData())
            ->setIsEuMember($data->country->isInEuropeanUnion)
            ->setIsoCode($data->country->isoCode)
            ->setName($data->country->name);
    }

    public function getDatabaseMetadata(string $database): ?Metadata
    {
        try {
            return $this->getDatabaseReader($database)->metadata();
        } catch (Throwable) {
            return null;
        }
    }

    public function getDatabasePath(string $database): string
    {
        return sprintf('%s/%s.mmdb', rtrim($this->config['targetDir'], '/'), $database);
    }

    public function getDatabaseSource(string $database): string
    {
        return sprintf('%s/%s', sys_get_temp_dir(), basename($this->config['databases'][$database]['source']));
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function getDatabaseReader(string $database): Reader
    {
        return new Reader(
            $this->getDatabasePath($database)
        );
    }

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getLocation(string $ipAddress): LocationData
    {
        $ipAddress = $this->obfuscateIpAddress($ipAddress);
        $asnData   = $this->getAsnReader()->asn($ipAddress);
        $cityData  = $this->getCityReader()->city($ipAddress);
        $continent = (new ContinentData())
            ->setCode($cityData->continent->code)
            ->setName($cityData->continent->name);

        $country = (new CountryData())
            ->setIsEuMember($cityData->country->isInEuropeanUnion)
            ->setIsoCode($cityData->country->isoCode)
            ->setName($cityData->country->name);

        $organization = (new OrganizationData())
            ->setAsn($asnData->autonomousSystemNumber)
            ->setName($asnData->autonomousSystemOrganization);

        return (new LocationData())
            ->setContinent($continent)
            ->setCountry($country)
            ->setLatitude($cityData->location->latitude)
            ->setLongitude($cityData->location->longitude)
            ->setOrganization($organization)
            ->setTimeZone($cityData->location->timeZone);
    }

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getOrganization(string $ipAddress): OrganizationData
    {
        $reader = $this->getDatabaseReader(self::DATABASE_ASN);
        $data   = $reader->asn($this->obfuscateIpAddress($ipAddress));

        return (new OrganizationData())
            ->setAsn($data->autonomousSystemNumber)
            ->setName($data->autonomousSystemOrganization);
    }

    /**
     * @throws Exception
     */
    public function obfuscateIpAddress(string $ipAddress): string
    {
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $separator = '.';
        } elseif (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $separator = ':';
        } else {
            throw new Exception('Invalid IP address: ' . $ipAddress);
        }

        $parts = explode($separator, $ipAddress);
        array_pop($parts);
        $parts[] = '0';

        return implode($separator, $parts);
    }
}
