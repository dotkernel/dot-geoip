<?php

declare(strict_types=1);

namespace Dot\GeoIP\Service;

use Dot\GeoIP\Data\CityData;
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

use function array_key_exists;
use function array_pop;
use function basename;
use function date;
use function explode;
use function file_exists;
use function filter_var;
use function implode;
use function rtrim;
use function sprintf;
use function str_replace;
use function sys_get_temp_dir;
use function trim;

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
        $path = $this->getRealFilePath($database);

        return file_exists($path);
    }

    public function getContinent(string $ipAddress): ContinentData
    {
        $continentData = new ContinentData();
        try {
            $data = $this->getCountryReader()->country($this->obfuscateIpAddress($ipAddress));
            $continentData->setCode($data->continent->code);
            $continentData->setName($data->continent->name);
        } catch (Throwable $exception) {
            $continentData->setError($exception->getMessage());
        }

        return $continentData;
    }

    public function getCountry(string $ipAddress): CountryData
    {
        $countryData = new CountryData();
        try {
            $data = $this->getCountryReader()->country($this->obfuscateIpAddress($ipAddress));
            $countryData->setIsoCode($data->country->isoCode);
            $countryData->setName($data->country->name);
            $countryData->setIsEuMember($data->country->isInEuropeanUnion);
        } catch (Throwable $exception) {
            $countryData->setError($exception->getMessage());
        }

        return $countryData;
    }

    public function getCity(string $ipAddress): CityData
    {
        $cityData = new CityData();

        try {
            $data = $this->getCityReader()->city($this->obfuscateIpAddress($ipAddress));
            $cityData->setName($data->city->name);
        } catch (Throwable $exception) {
            $cityData->setError($exception->getMessage());
        }

        return $cityData;
    }

    public function getDatabaseMetadata(string $database): ?Metadata
    {
        try {
            return $this->getDatabaseReader($database)->metadata();
        } catch (Throwable) {
            return null;
        }
    }

    public function getRealFilePath(string $database): string
    {
        return sprintf('%s/%s.mmdb', rtrim($this->config['targetDir'], '/'), $database);
    }

    public function getTempFilePath(string $database): string
    {
        return $this->parsePlaceholders(
            sprintf('%s/%s', sys_get_temp_dir(), basename($this->config['databases'][$database]['source']))
        );
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function getDatabaseReader(string $database): Reader
    {
        return new Reader(
            $this->getRealFilePath($database)
        );
    }

    /**
     * @throws Exception
     */
    public function getDatabaseSourceUrl(string $database): string
    {
        if (! array_key_exists($database, $this->config['databases'])) {
            throw new Exception('Database source URL not found');
        }

        return $this->parsePlaceholders($this->config['databases'][$database]['source']);
    }

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getLocation(string $ipAddress): LocationData
    {
        $ipAddress = $this->obfuscateIpAddress($ipAddress);

        $cityData = $this->getCityReader()->city($ipAddress);

        $continent = $this->getContinent($ipAddress);

        $country = $this->getCountry($ipAddress);

        $city = $this->getCity($ipAddress);

        $organization = $this->getOrganization($ipAddress);

        return (new LocationData())
            ->setContinent($continent)
            ->setCountry($country)
            ->setCity($city)
            ->setLatitude($cityData->location->latitude)
            ->setLongitude($cityData->location->longitude)
            ->setOrganization($organization)
            ->setTimeZone($cityData->location->timeZone);
    }

    /**
     * @throws Exception
     */
    public function getOrganization(string $ipAddress): OrganizationData
    {
        $organizationData = new OrganizationData();
        try {
            $data = $this->getAsnReader()->asn($this->obfuscateIpAddress($ipAddress));
            $organizationData->setAsn($data->autonomousSystemNumber);
            $organizationData->setName($data->autonomousSystemOrganization);
        } catch (Throwable $exception) {
            $organizationData->setError($exception->getMessage());
        }

        return $organizationData;
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

    private function parsePlaceholders(string $subject): string
    {
        $subject = str_replace('{year}', date('Y'), $subject);
        $subject = str_replace('{month}', date('m'), $subject);

        return trim($subject);
    }
}
