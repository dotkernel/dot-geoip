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
use MaxMind\Db\Reader\Metadata;

interface LocationServiceInterface
{
    public function getConfigs(): array;

    public function getConfig(string $name): mixed;

    public function databaseExists(string $database): bool;

    public function getContinent(string $ipAddress): ContinentData;

    public function getCountry(string $ipAddress): CountryData;

    public function getCity(string $ipAddress): CityData;

    public function getDatabaseMetadata(string $database): ?Metadata;

    public function getRealFilePath(string $database): string;

    public function getTempFilePath(string $database): string;

    public function getDatabaseReader(string $database): Reader;

    /**
     * @throws Exception
     */
    public function getDatabaseSourceUrl(string $database): string;

    public function getLocation(string $ipAddress): LocationData;

    public function getOrganization(string $ipAddress): OrganizationData;

    public function obfuscateIpAddress(string $ipAddress): string;
}
