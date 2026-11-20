<?php

declare(strict_types=1);

namespace Dot\GeoIP\Service;

use Dot\GeoIP\Data\CityData;
use Dot\GeoIP\Data\ContinentData;
use Dot\GeoIP\Data\CountryData;
use Dot\GeoIP\Data\LocationData;
use Dot\GeoIP\Data\OrganizationData;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use InvalidArgumentException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use MaxMind\Db\Reader\Metadata;

/**
 * @phpstan-import-type GeoIpConfig from LocationService
 */
interface LocationServiceInterface
{
    /**
     * @return GeoIpConfig
     */
    public function getConfigs(): array;

    public function getConfig(string $name): mixed;

    public function databaseExists(string $database): bool;

    public function getContinent(string $ipAddress): ContinentData;

    public function getCountry(string $ipAddress): CountryData;

    public function getCity(string $ipAddress): CityData;

    public function getDatabaseMetadata(string $database): ?Metadata;

    public function getRealFilePath(string $database): string;

    /**
     * @throws InvalidArgumentException
     */
    public function getTempFilePath(string $database): string;

    /**
     * @throws InvalidDatabaseException
     */
    public function getDatabaseReader(string $database): Reader;

    /**
     * @throws InvalidArgumentException
     */
    public function getDatabaseSourceUrl(string $database): string;

    /**
     * Unlike the other lookup methods, this method throws when the lookup fails.
     *
     * @throws InvalidArgumentException
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getLocation(string $ipAddress): LocationData;

    public function getOrganization(string $ipAddress): OrganizationData;

    /**
     * @throws InvalidArgumentException
     */
    public function obfuscateIpAddress(string $ipAddress): string;
}
