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
     * @param string $ipAddress
     * @return ContinentData
     * @throws Exception
     */
    function getContinent(string $ipAddress): ContinentData;

    /**
     * @param string $ipAddress
     * @return CountryData
     * @throws Exception
     */
    function getCountry(string $ipAddress): CountryData;

    /**
     * @param string $database
     * @return Metadata|null
     * @throws InvalidDatabaseException
     */
    function getDatabaseMetadata(string $database): ?Metadata;

    /**
     * @param string $ipAddress
     * @return LocationData
     */
    function getLocation(string $ipAddress): LocationData;

    /**
     * @param string $ipAddress
     * @return OrganizationData
     * @throws Exception
     */
    function getOrganization(string $ipAddress): OrganizationData;

    /**
     * @param string $identifier
     * @return bool
     */
    function isValidDatabaseIdentifier(string $identifier): bool;

    /**
     * @param string $ipAddress
     * @return string
     * @throws Exception
     */
    function obfuscateIpAddress(string $ipAddress): string;
}
