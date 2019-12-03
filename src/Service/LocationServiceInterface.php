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

/**
 * Interface LocationServiceInterface
 * @package Dot\GeoIP\Service
 */
interface LocationServiceInterface
{
    /**
     * @param string $database
     * @return bool
     */
    public function databaseExists(string $database): bool;

    /**
     * @param string $ipAddress
     * @return ContinentData
     * @throws Exception
     */
    public function getContinent(string $ipAddress): ContinentData;

    /**
     * @param string $ipAddress
     * @return CountryData
     * @throws Exception
     */
    public function getCountry(string $ipAddress): CountryData;

    /**
     * @param string $database
     * @return Metadata|null
     */
    public function getDatabaseMetadata(string $database): ?Metadata;

    /**
     * @param string $database
     * @return string
     */
    public function getDatabasePath(string $database): string;

    /**
     * @param string $database
     * @return Reader
     * @throws InvalidDatabaseException
     */
    public function getDatabaseReader(string $database): Reader;

    /**
     * @param string $ipAddress
     * @return LocationData
     */
    public function getLocation(string $ipAddress): LocationData;

    /**
     * @param string $ipAddress
     * @return OrganizationData
     * @throws Exception
     */
    public function getOrganization(string $ipAddress): OrganizationData;

    /**
     * @param string $ipAddress
     * @return string
     * @throws Exception
     */
    public function obfuscateIpAddress(string $ipAddress): string;
}
