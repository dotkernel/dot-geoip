<?php

declare(strict_types=1);

namespace Dot\GeoIP\Service;

use Dot\GeoIP\Data\LocationData;
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
    public function getDetails(string $ipAddress): LocationData;
}
