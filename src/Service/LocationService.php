<?php

declare(strict_types=1);

namespace Dot\GeoIP\Service;

use Dot\GeoIP\Data\ContinentData;
use Dot\GeoIP\Data\CountryData;
use Dot\GeoIP\Data\LocationData;
use Dot\GeoIP\Data\OrganizationData;
use Exception;
use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\InvalidDatabaseException;
use MaxMind\Db\Reader\Metadata;
use Throwable;

class LocationService implements LocationServiceInterface
{
    protected Reader $countryReader;
    protected Reader $cityReader;
    protected Reader $asnReader;
    public const DATABASE_ALL = 'all';
    public const DATABASE_ASN = 'asn';
    public const DATABASE_CITY = 'city';
    public const DATABASE_COUNTRY = 'country';
    public const DATABASES = [
        self::DATABASE_ASN => 'GeoLite2-ASN.mmdb',
        self::DATABASE_CITY => 'GeoLite2-City.mmdb',
        self::DATABASE_COUNTRY => 'GeoLite2-Country.mmdb'
    ];
    protected array $config = [];

    /**
     * @throws InvalidDatabaseException
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->countryReader = $this->getDatabaseReader(self::DATABASE_COUNTRY);
        $this->cityReader = $this->getDatabaseReader(self::DATABASE_COUNTRY);
        $this->asnReader = $this->getDatabaseReader(self::DATABASE_ASN);
    }

    public function getConfigs(): array
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(string $name)
    {
        return $this->config[$name] ?? null;
    }

    public function getCountryReader(): Reader
    {
        return $this->countryReader;
    }

    public function setCountryReader(Reader $countryReader): self
    {
        $this->countryReader = $countryReader;

        return $this;
    }

    public function getCityReader(): Reader
    {
        return $this->cityReader;
    }

    public function setCityReader(Reader $cityReader): self
    {
        $this->cityReader = $cityReader;

        return $this;
    }

    public function getAsnReader(): Reader
    {
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
     * @inheritDoc
     */
    public function getContinent(string $ipAddress): ContinentData
    {
        $data = $this->getCountryReader()->country($this->obfuscateIpAddress($ipAddress));

        return (new ContinentData())
            ->setCode($data->continent->code ?? null)
            ->setName($data->continent->name ?? null);
    }

    /**
     * @inheritDoc
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
        } catch (Throwable $exception) {
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
     * @inheritDoc
     */
    public function getDatabaseReader(string $database): Reader
    {
        return new Reader(
            $this->getDatabasePath($database)
        );
    }

    /**
     * @inheritDoc
     */
    public function getLocation(string $ipAddress): LocationData
    {
        $ipAddress = $this->obfuscateIpAddress($ipAddress);

        $asnData = $this->getAsnReader()->asn($ipAddress);
        $cityData = $this->getCityReader()->city($ipAddress);

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
     * @inheritDoc
     */
    public function getOrganization(string $ipAddress): OrganizationData
    {
        $reader = $this->getDatabaseReader(self::DATABASE_ASN);
        $data = $reader->asn($this->obfuscateIpAddress($ipAddress));

        return (new OrganizationData())
            ->setAsn($data->autonomousSystemNumber)
            ->setName($data->autonomousSystemOrganization);
    }

    /**
     * @inheritDoc
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
