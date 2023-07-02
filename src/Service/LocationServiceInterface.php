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

interface LocationServiceInterface
{
    public function getConfigs(): array;

    /**
     * @return mixed
     */
    public function getConfig(string $name);

    public function databaseExists(string $database): bool;

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getContinent(string $ipAddress): ContinentData;

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getCountry(string $ipAddress): CountryData;

    public function getDatabaseMetadata(string $database): ?Metadata;

    public function getDatabasePath(string $database): string;

    public function getDatabaseSource(string $database): string;

    /**
     * @throws InvalidDatabaseException
     */
    public function getDatabaseReader(string $database): Reader;

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getLocation(string $ipAddress): LocationData;

    /**
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getOrganization(string $ipAddress): OrganizationData;

    /**
     * @throws Exception
     */
    public function obfuscateIpAddress(string $ipAddress): string;
}
